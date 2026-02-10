<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Models\Approval;

class AttendanceRepository extends BaseRepository
{
    public function model()
    {
        return Attendance::class;
    }

    /*
    |----------------------------------------------------------------------
    | INDEX
    |----------------------------------------------------------------------
    */
    public function index($dataTable)
    {
        return $dataTable->render('admin.attendance.index');
    }

    /*
    |----------------------------------------------------------------------
    | CREATE FORM
    |----------------------------------------------------------------------
    */
    public function create(array $attributes = [])
    {
        return view('admin.attendance.create', [
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | STORE
    |----------------------------------------------------------------------
    */
    public function store($request)
    {
        DB::beginTransaction();

        try {

            $data = $request->only([
                'teacher_id',
                'date',
                'check_in',
                'check_out',
                'method_in',
                'method_out',
                'status',
                'reason',
                'late_duration',
            ]);

            $data['created_by_id'] = Auth::id();

            /*
            |------------------------------------------------------------------
            | IZIN / SAKIT / CUTI (MANUAL → APPROVAL)
            |------------------------------------------------------------------
            */
            if (in_array($data['status'], ['izin', 'sakit', 'cuti'])) {

                $proof = null;

                if ($request->hasFile('proof_file')) {
                    $proof = $request->file('proof_file')
                        ->store('approval/proofs', 'public');
                }

                Approval::create([
                    'teacher_id'    => $data['teacher_id'],
                    'start_date'    => $data['date'],
                    'end_date'      => $data['date'],
                    'type'          => $data['status'], // izin | sakit | cuti
                    'reason'        => $data['reason'],
                    'proof_file'    => $proof,
                    'status'        => 'pending',
                    'created_by_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()
                    ->route('admin.attendance.index')
                    ->with(
                        'success',
                        'Pengajuan ' . ucfirst($data['status']) . ' berhasil dikirim dan menunggu approval.'
                    );
            }

            /*
            |------------------------------------------------------------------
            | HADIR / TELAT / ALPHA → ATTENDANCE
            |------------------------------------------------------------------
            */

            // photo check-in
            if ($request->hasFile('photo_check_in')) {
                $data['photo_check_in'] = $request->file('photo_check_in')
                    ->store('attendance/checkin', 'public');
            }

            // photo check-out
            if ($request->hasFile('photo_check_out')) {
                $data['photo_check_out'] = $request->file('photo_check_out')
                    ->store('attendance/checkout', 'public');
            }

            // proof optional
            if ($request->hasFile('proof_file')) {
                $data['proof_file'] = $request->file('proof_file')
                    ->store('attendance/proofs', 'public');
            }

            Attendance::create($data);

            DB::commit();

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /*
    |----------------------------------------------------------------------
    | EDIT FORM
    |----------------------------------------------------------------------
    */
    public function edit($id)
    {
        $attendance = $this->model->findOrFail($id);

        return view('admin.attendance.edit', [
            'attendance' => $attendance,
            'teachers'   => Teacher::orderBy('name')->get(),
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | UPDATE
    |----------------------------------------------------------------------
    */
    public function update($request, $id)
    {
        DB::beginTransaction();

        try {

            $attendance = $this->model->findOrFail($id);

            $data = $request->only([
                'teacher_id',
                'date',
                'check_in',
                'check_out',
                'method_in',
                'method_out',
                'status',
                'reason',
                'late_duration',
            ]);

            // update photo check-in
            if ($request->hasFile('photo_check_in')) {
                $data['photo_check_in'] = $request->file('photo_check_in')
                    ->store('attendance/checkin', 'public');
            }

            // update photo check-out
            if ($request->hasFile('photo_check_out')) {
                $data['photo_check_out'] = $request->file('photo_check_out')
                    ->store('attendance/checkout', 'public');
            }

            // update proof
            if ($request->hasFile('proof_file')) {
                $data['proof_file'] = $request->file('proof_file')
                    ->store('attendance/proofs', 'public');
            }

            $attendance->update($data);

            DB::commit();

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance berhasil diupdate.');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /*
    |----------------------------------------------------------------------
    | DESTROY
    |----------------------------------------------------------------------
    */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $attendance = $this->model->findOrFail($id);
            $attendance->delete();

            DB::commit();

            return back()->with('success', 'Attendance berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}