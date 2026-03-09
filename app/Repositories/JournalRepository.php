<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class JournalRepository extends BaseRepository
{
    public function model()
    {
        return \App\Models\Journal::class;
    }

    /**
     * Render datatable index
     */
    public function index($dataTable)
    {
        return $dataTable->render('admin.journal.index');
    }

    /**
     * Menyiapkan data untuk form create
     */
    public function create(array $attributes = [])
    {
        $teachers = \App\Models\Teacher::all();
        $schedules = \App\Models\Schedule::all();

        return view('admin.journal.create', array_merge($attributes, compact('teachers', 'schedules')));
    }

    /**
     * Simpan data baru
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'teacher_id',
                'schedule_id',
                'description',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $journal = $this->model->create($data);

            // Tambahkan media photo
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');
            }

            DB::commit();
            return redirect()->route('admin.journal.index')->with('success', 'Created successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Menyiapkan data untuk form edit
     */
    public function edit($id)
    {
        $journal = $this->model->findOrFail($id);
        $teachers = \App\Models\Teacher::all();
        $schedules = \App\Models\Schedule::all();

        return view('admin.journal.edit', compact('journal', 'teachers', 'schedules'));
    }

    /**
     * Update data jurnal
     */
    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $journal = $this->model->findOrFail($id);

            $data = $request->only([
                'teacher_id',
                'schedule_id',
                'description',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();

            $journal->update($data);

            // Update media photo
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $journal->clearMediaCollection('photo');
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');
            }

            DB::commit();
            return redirect()->route('admin.journal.index')->with('success', 'Updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Hapus jurnal
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $journal = $this->model->findOrFail($id);
            $journal->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}