<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\DataTables\PointDataTable;
use App\Repositories\PointRepository;
use App\Http\Requests\CreatePointRequest;
use App\Http\Requests\UpdatePointRequest;
use Illuminate\Http\Request;

class PointController extends Controller
{
    protected $repository;

    public function __construct(PointRepository $repository)
    {
        $this->authorizeResource(Point::class, 'point');
        $this->repository = $repository;
    }

    public function index(PointDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreatePointRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Point $point)
    {
        return $this->repository->show($point);
    }

    public function edit(Point $point)
    {
        return $this->repository->edit($point->id);
    }

    public function update(UpdatePointRequest $request, Point $point)
    {
        return $this->repository->update($request, $point->id);
    }

    public function destroy(Point $point)
    {
        return $this->repository->destroy($point->id);
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
