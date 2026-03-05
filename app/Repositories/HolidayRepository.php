<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class HolidayRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Holiday::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.holiday.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.holiday.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {

            $data = $request->only([
                'name',
                'date',
                'description',
                'status'
            ]);

            $data['created_by_id'] = Auth::id();

            $this->model->create($data);

            DB::commit();

            return redirect()
                ->route('admin.holiday.index')
                ->with('success', 'Holiday created successfully');
        } catch (Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        return view('admin.holiday.edit', [
            'holiday' => $model,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {

            $model = $this->model->findOrFail($id);

            $data = $request->only([
                'name',
                'date',
                'description',
                'status'
            ]);

            $data['created_by_id'] = Auth::id();

            $model->update($data);

            DB::commit();

            return redirect()
                ->route('admin.holiday.index')
                ->with('success', 'Holiday updated successfully');
        } catch (Exception $e) {

            DB::rollBack();
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
                ->with('success', 'Holiday deleted successfully');
        } catch (Exception $e) {

            DB::rollBack();

            return back()->with([
                'error' => $e->getMessage()
            ]);
        }
    }
}