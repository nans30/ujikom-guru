<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\DataTables\ItemDataTable;
use App\Repositories\ItemRepository;
use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->authorizeResource(Item::class, 'item');
        $this->repository = $repository;
    }

    public function index(ItemDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateItemRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Item $item)
    {
        return $this->repository->show($item);
    }

    public function edit(Item $item)
    {
        return $this->repository->edit($item->id);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        return $this->repository->update($request, $item->id);
    }

    public function destroy(Item $item)
    {
        return $this->repository->destroy($item->id);
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
