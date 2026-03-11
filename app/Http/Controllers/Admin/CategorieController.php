<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\DataTables\CategorieDataTable;
use App\Repositories\CategorieRepository;
use App\Http\Requests\CreateCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    protected $repository;

    public function __construct(CategorieRepository $repository)
    {
        $this->authorizeResource(Categorie::class, 'categorie');
        $this->repository = $repository;
    }

    public function index(CategorieDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateCategorieRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Categorie $categorie)
    {
        return $this->repository->show($categorie);
    }

    public function edit(Categorie $categorie)
    {
        return $this->repository->edit($categorie->id);
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        return $this->repository->update($request, $categorie->id);
    }

    public function destroy(Categorie $categorie)
    {
        return $this->repository->destroy($categorie->id);
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
