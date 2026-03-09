<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class ScheduleRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Schedule::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.schedule.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.schedule.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'teacher_id',
                'subject',
                'class_name',
                'day_of_week',
                'start_time',
                'end_time',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $model = $this->model->create($data);

            DB::commit();
            return redirect()->route('admin.schedule.index')->with('success', 'Schedule created successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        return view('admin.schedule.edit', [
            'schedule' => $model,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $model = $this->model->findOrFail($id);

            $data = $request->only([
                'teacher_id',
                'subject',
                'class_name',
                'day_of_week',
                'start_time',
                'end_time',
                'status',
            ]);

            $model->update($data);

            DB::commit();
            return redirect()->route('admin.schedule.index')->with('success', 'Schedule updated successfully');
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
            return redirect()->back()->with('success', 'Schedule deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}