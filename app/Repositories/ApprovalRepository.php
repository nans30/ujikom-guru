<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Approval;
use App\Models\Attendance;

class ApprovalRepository extends BaseRepository
{
    /*
    |--------------------------------------------------------------------------
    | MODEL
    |--------------------------------------------------------------------------
    */
    public function model()
    {
        return Approval::class;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index($dataTable)
    {
        return $dataTable->render('admin.approval.index');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE APPROVAL (ADMIN MANUAL INPUT)
    |--------------------------------------------------------------------------
    | - Simpan approval (pending)
    | - Bisa multi hari (start_date - end_date)
    */
    public function createApproval($request)
    {
        return DB::transaction(function () use ($request) {

            return $this->model->create([
                'teacher_id' => $request->teacher_id,
                'type'       => $request->type,        // izin | sakit | cuti | dinas
                'reason'     => $request->reason,
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'proof_file' => $request->proof_file ?? null,
                'status'     => 'pending',
                'created_by' => Auth::id(),
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    | - Update status approval
    | - Generate attendance per tanggal
    */
    public function approve(int $id)
    {
        DB::transaction(function () use ($id) {

            $approval = $this->model
                ->lockForUpdate()
                ->findOrFail($id);

            // prevent double approve
            if ($approval->status === 'approved') {
                return;
            }

            // update approval
            $approval->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // loop tanggal
            $period = CarbonPeriod::create(
                $approval->start_date,
                $approval->end_date
            );

            foreach ($period as $date) {

                Attendance::updateOrCreate(
                    [
                        'teacher_id' => $approval->teacher_id,
                        'date'       => $date->toDateString(),
                    ],
                    [
                        // jika DINAS → tetap HADIR
                        'status'        => $approval->type === 'dinas' ? 'hadir' : $approval->type,
                        'reason'        => $approval->type === 'dinas' ? 'Dinas' : $approval->reason,
                        'proof_file'    => $approval->proof_file ?? null,
                        'created_by_id' => Auth::id(),

                        // kosongkan field absensi fisik
                        'check_in'        => null,
                        'check_out'       => null,
                        'method_in'       => null,
                        'method_out'      => null,
                        'photo_check_in'  => null,
                        'photo_check_out' => null,
                        'late_duration'   => null,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.approval.index')
            ->with('success', 'Approval berhasil disetujui & attendance dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    | - Update approval saja
    | - Tidak buat attendance
    */
    public function reject(int $id)
    {
        DB::transaction(function () use ($id) {

            $approval = $this->model
                ->lockForUpdate()
                ->findOrFail($id);

            // prevent double reject
            if ($approval->status === 'rejected') {
                return;
            }

            $approval->update([
                'status'      => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.approval.index')
            ->with('success', 'Approval berhasil ditolak.');
    }
}