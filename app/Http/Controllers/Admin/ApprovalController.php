<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\DataTables\ApprovalDataTable;
use App\Repositories\ApprovalRepository;
use App\Http\Requests\CreateApprovalRequest;
use App\Http\Requests\UpdateApprovalRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $repo;

    public function __construct(ApprovalRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(ApprovalDataTable $dataTable)
    {
        return $this->repo->index($dataTable);
    }

    public function approve($id)
    {
        return $this->repo->approve($id);
    }

    public function reject($id)
    {
        return $this->repo->reject($id);
    }
    public function show($id)
    {
        $approval = Approval::with(['teacher'])->findOrFail($id);

        return view('admin.approval.show', compact('approval'));
    }
}