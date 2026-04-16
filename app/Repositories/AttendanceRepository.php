<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Models\Approval;

use App\Traits\AttendancePointTrait;

class AttendanceRepository extends BaseRepository
{
    use AttendancePointTrait;
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
        // Ambil jam batas hadir dari aturan poin
        $hadirRule = \App\Models\Point::where('name', 'hadir')->where('status', 1)->first();
        $thresholdTime = '08:00'; // default
        if ($hadirRule && $hadirRule->condition_operator === 'BETWEEN') {
            $parts = explode('-', str_replace(',', '-', $hadirRule->condition_value));
            if (count($parts) >= 2) $thresholdTime = trim($parts[1]);
        }

        return view('admin.attendance.create', [
            'teachers'      => Teacher::orderBy('name')->get(),
            'thresholdTime' => $thresholdTime,
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

            // Kalkulasi Poin & Menit Telat
            $time = !empty($data['check_in']) ? \Carbon\Carbon::parse($data['check_in'])->format('H:i:s') : null;
            $data['late_duration'] = $this->calculateLateMinutes($time);
            
            $pointData = $this->calculateAttendancePoints($time, $data['status'], false);
            $data['point_earned'] = $pointData['points'];
            
            $attendance = Attendance::create($data);

            // Update Saldo Guru & Log Ledger
            if ($pointData['points'] != 0) {
                $teacher = Teacher::findOrFail($data['teacher_id']);
                $teacher->point_balance += $pointData['points'];
                $teacher->save();

                \App\Models\PointLedger::create([
                    'teacher_id'       => $teacher->id,
                    'transaction_type' => $pointData['points'] > 0 ? 'EARN' : 'PENALTY',
                    'amount'           => $pointData['points'],
                    'current_balance'  => $teacher->point_balance,
                    'description'      => 'Absensi Manual Admin (' . ucfirst($data['status']) . '): ' . implode(', ', $pointData['descriptions']),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance berhasil disimpan dengan perolehan ' . $pointData['points'] . ' poin.');
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

        // Ambil jam batas hadir dari aturan poin
        $hadirRule = \App\Models\Point::where('name', 'hadir')->where('status', 1)->first();
        $thresholdTime = '08:00'; // default
        if ($hadirRule && $hadirRule->condition_operator === 'BETWEEN') {
            $parts = explode('-', str_replace(',', '-', $hadirRule->condition_value));
            if (count($parts) >= 2) $thresholdTime = trim($parts[1]);
        }

        return view('admin.attendance.edit', [
            'attendance'    => $attendance,
            'teachers'      => Teacher::orderBy('name')->get(),
            'thresholdTime' => $thresholdTime,
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
            'teacher_id', 'date', 'check_in', 'check_out',
            'method_in', 'method_out', 'status', 'reason', 'late_duration',
        ]);

        // Logika File: Simpan file baru jika ada, jika tidak ada tetap pakai yang lama
        if ($request->hasFile('photo_check_in')) {
            $data['photo_check_in'] = $request->file('photo_check_in')->store('attendance/checkin', 'public');
        }
        if ($request->hasFile('photo_check_out')) {
            $data['photo_check_out'] = $request->file('photo_check_out')->store('attendance/checkout', 'public');
        }
        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->file('proof_file')->store('attendance/proofs', 'public');
        }

        // Jika status diubah ke Sakit/Izin/Cuti saat edit, 
        // pastikan check_in & check_out diset NULL jika admin tidak mengisinya.
        if (in_array($data['status'], ['izin', 'sakit', 'cuti', 'alpha'])) {
            $data['late_duration'] = 0;
            // $data['check_in'] = null; // Aktifkan jika ingin otomatis hapus jam saat izin
            // $data['check_out'] = null;
        }

        // ====================================================================
        // POINT ADJUSTMENT LOGIC
        // ====================================================================
        $oldPoint = $attendance->point_earned;
        $oldTeacherId = $attendance->teacher_id;
        
        $newTime = !empty($data['check_in']) ? \Carbon\Carbon::parse($data['check_in'])->format('H:i:s') : null;
        $data['late_duration'] = $this->calculateLateMinutes($newTime);
        
        $newPointData = $this->calculateAttendancePoints($newTime, $data['status'], $attendance->is_token_used);
        $newPoint = $newPointData['points'];

        $data['point_earned'] = $newPoint;
        $attendance->update($data);

        // Jika ada perubahan poin atau perubahan guru
        if ($oldPoint != $newPoint || $oldTeacherId != $data['teacher_id']) {
            
            // 1. Tarik poin dari guru lama (jika ada)
            $oldTeacher = Teacher::find($oldTeacherId);
            if ($oldTeacher && $oldPoint != 0) {
                $oldTeacher->point_balance -= $oldPoint;
                $oldTeacher->save();

                \App\Models\PointLedger::create([
                    'teacher_id'       => $oldTeacher->id,
                    'transaction_type' => $oldPoint > 0 ? 'PENALTY' : 'EARN',
                    'amount'           => -$oldPoint,
                    'current_balance'  => $oldTeacher->point_balance,
                    'description'      => 'Koreksi Poin: Absensi diubah/dihapus oleh Admin',
                ]);
            }

            // 2. Berikan poin ke guru baru/saat ini
            $newTeacher = Teacher::find($data['teacher_id']);
            if ($newTeacher && $newPoint != 0) {
                $newTeacher->point_balance += $newPoint;
                $newTeacher->save();

                \App\Models\PointLedger::create([
                    'teacher_id'       => $newTeacher->id,
                    'transaction_type' => $newPoint > 0 ? 'EARN' : 'PENALTY',
                    'amount'           => $newPoint,
                    'current_balance'  => $newTeacher->point_balance,
                    'description'      => 'Koreksi Poin: Absensi diperbarui oleh Admin (' . ucfirst($data['status']) . ')',
                ]);
            }
        }

        DB::commit();
        return redirect()->route('admin.attendance.index')->with('success', 'Data absensi berhasil diperbarui. Poin disesuaikan.');
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

            // Tarik poin kembali jika ada poin yang pernah didapat
            if ($attendance->point_earned != 0) {
                $teacher = Teacher::find($attendance->teacher_id);
                if ($teacher) {
                    $teacher->point_balance -= $attendance->point_earned;
                    $teacher->save();

                    \App\Models\PointLedger::create([
                        'teacher_id'       => $teacher->id,
                        'transaction_type' => $attendance->point_earned > 0 ? 'PENALTY' : 'EARN',
                        'amount'           => -$attendance->point_earned,
                        'current_balance'  => $teacher->point_balance,
                        'description'      => 'Koreksi Poin: Absensi dihapus oleh Admin',
                    ]);
                }
            }

            $attendance->forceDelete();

            DB::commit();

            return back()->with('success', 'Attendance berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}