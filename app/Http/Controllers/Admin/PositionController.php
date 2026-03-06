<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\DataTables\PositionDataTable;
use App\Repositories\PositionRepository;
use App\Http\Requests\CreatePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    protected $repository;

    public function __construct(PositionRepository $repository)
    {
        $this->authorizeResource(Position::class, 'position');
        $this->repository = $repository;
    }

    public function index(PositionDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreatePositionRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Position $position)
    {
        return $this->repository->show($position);
    }

    public function edit(Position $position)
    {
        return $this->repository->edit($position->id);
    }

    public function update(UpdatePositionRequest $request, Position $position)
    {
        return $this->repository->update($request, $position->id);
    }

    public function destroy(Position $position)
    {
        return $this->repository->destroy($position->id);
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
