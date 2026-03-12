<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('teacher')->get();
        return response()->json([
            'status' => 'success',
            'data' => $attendances
        ]);
    }

    public function store(Request $request)
    {
        /* |--------------------------------------------------------------------------
         | DEBUGGING DATA
         |--------------------------------------------------------------------------
         | Jika ada parameter ?debug=true, tampilkan data yang dikirim dan hentikan eksekusi
         */
        if ($request->has('debug') && $request->debug == 'true') {
            dd('debuging', $request->all());
        }

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,telat,alpha,izin,sakit,cuti',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['teacher_id', 'date', 'check_in', 'check_out', 'method_in', 'method_out', 'status', 'reason', 'late_duration']);
            $data['created_by_id'] = Auth::id() ?? 1;

            if (in_array($data['status'], ['izin', 'sakit', 'cuti'])) {
                $proof = null;
                if ($request->hasFile('proof_file')) {
                    $proof = $request->file('proof_file')->store('approval/proofs', 'public');
                }

                $approval = Approval::create([
                    'teacher_id' => $data['teacher_id'],
                    'start_date' => $data['date'],
                    'end_date' => $data['date'],
                    'type' => $data['status'],
                    'reason' => $data['reason'] ?? '',
                    'proof_file' => $proof,
                    'status' => 'pending',
                    'created_by_id' => $data['created_by_id'],
                ]);

                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Pengajuan berhasil dikirim', 'data' => $approval]);
            }

            if ($request->hasFile('photo_check_in')) {
                $data['photo_check_in'] = $request->file('photo_check_in')->store('attendance/checkin', 'public');
            }
            if ($request->hasFile('photo_check_out')) {
                $data['photo_check_out'] = $request->file('photo_check_out')->store('attendance/checkout', 'public');
            }
            if ($request->hasFile('proof_file')) {
                $data['proof_file'] = $request->file('proof_file')->store('attendance/proofs', 'public');
            }

            $attendance = Attendance::create($data);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Attendance berhasil disimpan', 'data' => $attendance]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $attendance = Attendance::with('teacher')->find($id);
        if (!$attendance) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $attendance]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        DB::beginTransaction();
        try {
            $data = $request->only(['teacher_id', 'date', 'check_in', 'check_out', 'method_in', 'method_out', 'status', 'reason', 'late_duration']);

            if ($request->hasFile('photo_check_in')) {
                $data['photo_check_in'] = $request->file('photo_check_in')->store('attendance/checkin', 'public');
            }
            if ($request->hasFile('photo_check_out')) {
                $data['photo_check_out'] = $request->file('photo_check_out')->store('attendance/checkout', 'public');
            }
            if ($request->hasFile('proof_file')) {
                $data['proof_file'] = $request->file('proof_file')->store('attendance/proofs', 'public');
            }

            if (isset($data['status']) && in_array($data['status'], ['izin', 'sakit', 'cuti', 'alpha'])) {
                $data['late_duration'] = 0;
            }

            $attendance->update($data);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data absensi berhasil diperbarui', 'data' => $attendance]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }
        
        $attendance->delete();
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
