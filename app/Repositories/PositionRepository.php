<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class PositionRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Position::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.position.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.position.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'name',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $this->model->create($data);

            DB::commit();
            return redirect()->route('admin.position.index')->with('success', 'Created successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        return view('admin.position.edit', [
            'position' => $model,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $model = $this->model->findOrFail($id);

            $data = $request->only([
                'name',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $model->update($data);

            DB::commit();
            return redirect()->route('admin.position.index')->with('success', 'Updated successfully');
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
            return redirect()->back()->with('success', 'Deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}