<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class CategorieRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Categorie::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.categorie.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.categorie.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {

            $data = $request->only([
                'name',
                'description',
                'status'
            ]);

            $data['created_by_id'] = Auth::id();

            $this->model->create($data);

            DB::commit();

            return redirect()
                ->route('admin.categorie.index')
                ->with('success', 'Category created successfully');
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        return view('admin.categorie.edit', [
            'categorie' => $model,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {

            $model = $this->model->findOrFail($id);

            $data = $request->only([
                'name',
                'description',
                'status'
            ]);

            $model->update($data);

            DB::commit();

            return redirect()
                ->route('admin.categorie.index')
                ->with('success', 'Category updated successfully');
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $model = $this->model->findOrFail($id);
            $model->delete();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Category deleted successfully');
        } catch (Exception $e) {

            DB::rollback();

            return back()->with([
                'error' => $e->getMessage()
            ]);
        }
    }
}
