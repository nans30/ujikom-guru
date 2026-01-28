<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\DataTables\AttendanceDataTable;
use App\Repositories\AttendanceRepository;
use App\Http\Requests\CreateAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $repository;

    public function __construct(AttendanceRepository $repository)
    {
        $this->authorizeResource(Attendance::class, 'attendance');
        $this->repository = $repository;
    }

    public function index(AttendanceDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateAttendanceRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Attendance $attendance)
    {
        return $this->repository->show($attendance);
    }

    public function edit(Attendance $attendance)
    {
        return $this->repository->edit($attendance->id);
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        return $this->repository->update($request, $attendance->id);
    }

    public function destroy(Attendance $attendance)
    {
        return $this->repository->destroy($attendance->id);
    }

    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'No IDs selected');
        }

        return $this->repository->bulkDelete($ids);
    }

    public function copy($id)
    {
        return $this->repository->edit($id, true);
    }
}
