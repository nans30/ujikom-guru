<?php

namespace App\Repositories;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class ItemRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Item::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.item.index');
    }

    public function create(array $attributes = [])
    {
        return view('admin.item.create', $attributes);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'item_name',
                'point_cost',
                'extra_minutes',
                'stock_limit',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $this->model->create($data);

            DB::commit();
            return redirect()->route('admin.item.index')->with('success', 'Item berhasil ditambahkan');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        return view('admin.item.edit', [
            'item' => $model,
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $model = $this->model->findOrFail($id);

            $data = $request->only([
                'item_name',
                'point_cost',
                'extra_minutes',
                'stock_limit',
                'status',
            ]);

            $data['created_by_id'] = Auth::id();

            $model->update($data);

            DB::commit();
            return redirect()->route('admin.item.index')->with('success', 'Item berhasil diperbarui');
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

            // Redirect kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back()->with('success', 'Aturan poin berhasil dihapus');
        } catch (Exception $e) {
            DB::rollback();

            // Kembali dengan membawa pesan error
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}
