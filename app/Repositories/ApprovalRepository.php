<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Approval;
use App\Models\Attendance;

class ApprovalRepository extends BaseRepository
{
    public function model()
    {
        return Approval::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.approval.index');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $approval = $this->model->findOrFail($id);

            $approval->update([
                'status' => 'approved',
                'approved_by_id' => Auth::id(),
                'approved_at' => now(),
            ]);

            Attendance::updateOrCreate(
                [
                    'teacher_id' => $approval->teacher_id,
                    'date' => $approval->date,
                ],
                [
                    'status' => $approval->type,
                    'reason' => $approval->reason,
                    'proof_file' => $approval->proof_file,
                    'created_by_id' => Auth::id(), // ✅ FIX
                ]
            );
        });

        return redirect()
            ->route('admin.approval.index')
            ->with('success', 'Approved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject($id)
    {
        DB::transaction(function () use ($id) {

            $approval = $this->model->findOrFail($id);

            $approval->update([
                'status' => 'rejected',
                'approved_by_id' => Auth::id(),
                'approved_at' => now(),
            ]);

            Attendance::updateOrCreate(
                [
                    'teacher_id' => $approval->teacher_id,
                    'date' => $approval->date,
                ],
                [
                    'status' => 'alpha',
                    'created_by_id' => Auth::id(),
                ]
            );
        });

        return redirect()
            ->route('admin.approval.index')
            ->with('success', 'Rejected → set alpha');
    }
}