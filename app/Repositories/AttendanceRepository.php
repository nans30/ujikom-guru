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

    public function index($dataTable)
    {
        return $dataTable->render('admin.attendance.index');
    }

    /**
     * Show create form
     */
    public function create(array $attributes = [])
    {
        $attributes['teachers'] = Teacher::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.attendance.create', $attributes);
    }

    /**
     * Store attendance
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
        =========================================
        JIKA IZIN / SAKIT → MASUK APPROVAL DULU
        =========================================
        */
            if (in_array($data['status'], ['izin', 'sakit'])) {

                $proof = null;

                if ($request->hasFile('proof_file')) {
                    $proof = $request->file('proof_file')
                        ->store('attendance/proofs', 'public');
                }

                Approval::create([
                    'teacher_id' => $data['teacher_id'],
                    'date' => $data['date'],
                    'type' => $data['status'],
                    'reason' => $data['reason'],
                    'proof_file' => $proof,
                    'status' => 'pending',
                    'created_by_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()
                    ->route('admin.attendance.index')
                    ->with('success', 'Menunggu approval');
            }

            /*
        =========================================
        NORMAL ATTENDANCE (hadir/telat/alpha)
        =========================================
        */

            // ✅ HANDLE PHOTO CHECK IN
            if ($request->hasFile('photo_check_in')) {
                $data['photo_check_in'] = $request->file('photo_check_in')
                    ->store('attendance/checkin', 'public');
            }

            // ✅ HANDLE PHOTO CHECK OUT
            if ($request->hasFile('photo_check_out')) {
                $data['photo_check_out'] = $request->file('photo_check_out')
                    ->store('attendance/checkout', 'public');
            }

            // ✅ HANDLE PROOF FILE (opsional)
            if ($request->hasFile('proof_file')) {
                $data['proof_file'] = $request->file('proof_file')
                    ->store('attendance/proofs', 'public');
            }

            $this->model->create($data);

            DB::commit();

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Show edit form
     */
    public function edit($id)
    {
        $attendance = $this->model->findOrFail($id);

        return view('admin.attendance.edit', [
            'attendance' => $attendance,
            'teachers'   => Teacher::select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update attendance
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

            // siapa yang update
            $data['created_by_id'] = Auth::id() ?? $attendance->created_by_id;

            /* ========================
               UPDATE PHOTO CHECK IN
            ======================== */
            if ($request->hasFile('photo_check_in')) {
                $data['photo_check_in'] = $request->file('photo_check_in')
                    ->store('attendance/checkin', 'public');
            }

            /* ========================
               UPDATE PHOTO CHECK OUT
            ======================== */
            if ($request->hasFile('photo_check_out')) {
                $data['photo_check_out'] = $request->file('photo_check_out')
                    ->store('attendance/checkout', 'public');
            }

            /* ========================
               UPDATE PROOF FILE
            ======================== */
            if ($request->hasFile('proof_file')) {
                $data['proof_file'] = $request->file('proof_file')
                    ->store('attendance/proofs', 'public');
            }

            $attendance->update($data);

            DB::commit();
            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete attendance
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $attendance = $this->model->findOrFail($id);
            $attendance->delete();

            DB::commit();
            return redirect()
                ->back()
                ->with('success', 'Attendance deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}