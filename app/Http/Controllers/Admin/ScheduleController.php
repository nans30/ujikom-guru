<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\DataTables\ScheduleDataTable;
use App\Repositories\ScheduleRepository;
use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $repository;

    public function __construct(ScheduleRepository $repository)
    {
        $this->authorizeResource(Schedule::class, 'schedule');
        $this->repository = $repository;
    }

    public function index(ScheduleDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateScheduleRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Schedule $schedule)
    {
        return $this->repository->show($schedule);
    }

    public function edit(Schedule $schedule)
    {
        return $this->repository->edit($schedule->id);
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        return $this->repository->update($request, $schedule->id);
    }

    public function destroy(Schedule $schedule)
    {
        return $this->repository->destroy($schedule->id);
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
