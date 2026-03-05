<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\DataTables\HolidayDataTable;
use App\Repositories\HolidayRepository;
use App\Http\Requests\CreateHolidayRequest;
use App\Http\Requests\UpdateHolidayRequest;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    protected $repository;

    public function __construct(HolidayRepository $repository)
    {
        $this->authorizeResource(Holiday::class, 'holiday');
        $this->repository = $repository;
    }

    public function index(HolidayDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateHolidayRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Holiday $holiday)
    {
        return $this->repository->show($holiday);
    }

    public function edit(Holiday $holiday)
    {
        return $this->repository->edit($holiday->id);
    }

    public function update(UpdateHolidayRequest $request, Holiday $holiday)
    {
        return $this->repository->update($request, $holiday->id);
    }

    public function destroy(Holiday $holiday)
    {
        return $this->repository->destroy($holiday->id);
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
