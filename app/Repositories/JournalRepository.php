<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use App\Models\Journal;

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
            $teacherId = $request->teacher_id;
            $scheduleId = $request->schedule_id;
            $today = Carbon::today()->format('Y-m-d');

            // --- PERBAIKAN: Validasi Cegah Double Input ---
            $exists = $this->model->where('schedule_id', $scheduleId)
                ->where('date', $today)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jurnal untuk jadwal ini sudah diisi hari ini!');
            }

            $data = $request->only([
                'teacher_id',
                'schedule_id',
                'description',
                'status',
            ]);
            $data['created_by_id'] = Auth::id();
            $data['date'] = $today;

            $journal = $this->model->create($data);

            // Tambahkan media photo
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');

                // UPDATE photo_url agar sinkron ke Frontend
                $journal->update([
                    'photo_url' => $journal->getFirstMediaUrl('photo')
                ]);
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

            // Jika schedule_id diubah, cek apakah jadwal baru itu sudah ada isinya hari ini
            if ($request->schedule_id != $journal->schedule_id) {
                $exists = $this->model->where('schedule_id', $request->schedule_id)
                    ->where('date', $journal->date)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal update! Jadwal tujuan sudah memiliki jurnal di tanggal tersebut.');
                }
            }

            $data = $request->only([
                'teacher_id',
                'schedule_id',
                'description',
                'status',
            ]);

            $journal->update($data);

            // Update media photo
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $journal->clearMediaCollection('photo');
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');

                // UPDATE photo_url agar link di database tidak pecah/lama
                $journal->update([
                    'photo_url' => $journal->getFirstMediaUrl('photo')
                ]);
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