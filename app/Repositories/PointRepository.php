<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class PointRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Point::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.point.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.point.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'name',
                'condition_operator',
                'condition_value',
                'point_modifier',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $model = $this->model->create($data);

            // Media Handling (Jika menggunakan Spatie Media Library)
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $model->addMediaFromRequest('image')->toMediaCollection('image');
            }

            DB::commit();
            return redirect()->route('admin.point.index')->with('success', 'Aturan poin berhasil ditambahkan');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        return view('admin.point.edit', [
            'point' => $model,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $model = $this->model->findOrFail($id);

            $data = $request->only([
                'name',
                'condition_operator',
                'condition_value',
                'point_modifier',
                'status',
            ]);

            // Opsional: perbarui siapa yang terakhir mengedit
            $data['created_by_id'] = Auth::id();

            $model->update($data);

            // Update Media
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $model->clearMediaCollection('image');
                $model->addMediaFromRequest('image')->toMediaCollection('image');
            }

            DB::commit();
            return redirect()->route('admin.point.index')->with('success', 'Aturan poin berhasil diperbarui');
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
            return redirect()->back()->with('success', 'Aturan poin berhasil dihapus');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}
