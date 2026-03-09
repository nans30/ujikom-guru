<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) return redirect()->back()->with('error', 'Data guru tidak ditemukan.');

        $journals = Journal::with(['schedule'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('frontend.journal.index', compact('journals'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) return redirect()->back()->with('error', 'Akses ditolak.');

        // Ambil nama hari singkat (Mon, Tue, Wed, dst)
        $todayName = Carbon::now()->format('D');

        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->where('status', 1)
            ->get();

        $completedScheduleIds = Journal::where('teacher_id', $teacher->id)
            ->where('date', Carbon::today()->format('Y-m-d'))
            ->pluck('schedule_id')
            ->toArray();

        return view('frontend.journal.create', compact('schedules', 'completedScheduleIds'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $schedule = Schedule::findOrFail($request->schedule_id);
        $now = now()->format('H:i:s');

        // Validasi: Jangan sampai isi SEBELUM jam pelajaran mulai
        if ($now < $schedule->start_time) {
            return redirect()->back()->with('error', 'Sabar! Jam pelajaran belum dimulai (Mulai: ' . $schedule->start_time . ').');
        }

        // Cek double input
        $exists = Journal::where('schedule_id', $request->schedule_id)
            ->where('date', Carbon::today()->format('Y-m-d'))
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Jurnal sudah diisi hari ini.');
        }

        $journal = Journal::create([
            'teacher_id'    => $teacher->id,
            'schedule_id'   => $request->schedule_id,
            'date'          => Carbon::today()->format('Y-m-d'),
            'description'   => $request->description,
            'status'        => 1,
            'created_by_id' => Auth::id(),
        ]);

        if ($request->hasFile('photo')) {
            // SINKRON: Menggunakan koleksi 'photo' sama dengan Admin
            $journal->addMediaFromRequest('photo')->toMediaCollection('photo');

            // Update photo_url manual untuk cadangan kolom database
            $journal->update(['photo_url' => $journal->getFirstMediaUrl('photo')]);
        }

        return redirect()->route('journal.index')->with('success', 'Jurnal berhasil disimpan!');
    }

    public function edit($id)
    {
        $journal = Journal::findOrFail($id);

        // Proteksi agar tidak bisa edit jurnal orang lain
        if ($journal->teacher_id != Auth::user()->teacher->id) {
            abort(403, 'Anda tidak memiliki akses ke jurnal ini.');
        }

        return view('frontend.journal.edit', compact('journal'));
    }

    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);

        // Proteksi akses
        if ($journal->teacher_id != Auth::user()->teacher->id) {
            abort(403);
        }

        $request->validate([
            'description' => 'required|min:5', // Sesuaikan min karakter
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Update deskripsi
        $journal->description = $request->description;

        // Proses foto jika ada upload baru
        if ($request->hasFile('photo')) {
            try {
                // Hapus media lama (Spatie Media Library)
                $journal->clearMediaCollection('photo');

                // Simpan media baru
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');

                // Update kolom photo_url dengan URL terbaru
                $journal->photo_url = $journal->getFirstMediaUrl('photo');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
            }
        }

        $journal->save();

        return redirect()->route('journal.index')->with('success', 'Jurnal berhasil diperbarui!');
    }
}