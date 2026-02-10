<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Teacher;
use App\DataTables\ApprovalDataTable;
use App\Repositories\ApprovalRepository;
use App\Http\Requests\CreateApprovalRequest;
use App\Http\Requests\UpdateApprovalRequest;

class ApprovalController extends Controller
{
    protected $repo;

    public function __construct(ApprovalRepository $repo)
    {
        $this->repo = $repo;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(ApprovalDataTable $dataTable)
    {
        return $this->repo->index($dataTable);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE (ADMIN MANUAL INPUT)
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.approval.create', [
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(CreateApprovalRequest $request)
    {
        $this->repo->createApproval($request);

        return redirect()
            ->route('admin.approval.index')
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $approval = Approval::with(['teacher', 'approver'])->findOrFail($id);

        return view('admin.approval.show', compact('approval'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT (SEBELUM APPROVE)
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $approval = Approval::findOrFail($id);

        // ❗ tidak boleh edit jika sudah diputuskan
        if ($approval->status !== 'pending') {
            abort(403, 'Approval sudah diproses');
        }

        return view('admin.approval.edit', [
            'approval' => $approval,
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(UpdateApprovalRequest $request, $id)
    {
        $approval = Approval::findOrFail($id);

        if ($approval->status !== 'pending') {
            abort(403, 'Approval sudah diproses');
        }

        $approval->update($request->only([
            'teacher_id',
            'date',
            'type',
            'reason',
        ]));

        return redirect()
            ->route('admin.approval.index')
            ->with('success', 'Approval berhasil diupdate.');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve($id)
    {
        return $this->repo->approve($id);
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject($id)
    {
        return $this->repo->reject($id);
    }
}