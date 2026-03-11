<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Library Intervension Image v3 dihapus karena tidak ingin kompres lagi

class JournalController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) return redirect()->back()->with('error', 'Data guru tidak ditemukan.');

        $journals = Journal::with(['schedule', 'media'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('frontend.journal.index', compact('journals'));
    }

    public function create(Request $request)
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) return redirect()->back()->with('error', 'Akses ditolak.');

        $todayName = Carbon::now()->format('D');

        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->where('status', 1)
            ->get();

        $completedScheduleIds = Journal::where('teacher_id', $teacher->id)
            ->where('date', Carbon::today()->format('Y-m-d'))
            ->pluck('schedule_id')
            ->toArray();

        $selectedScheduleId = $request->get('schedule_id');

        return view('frontend.journal.create', compact('schedules', 'completedScheduleIds', 'selectedScheduleId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required',
            'description' => 'required|min:5',
            'photo'       => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ], [
            'photo.required' => 'Anda harus mengunggah foto bukti mengajar.',
            'description.required' => 'Ringkasan materi tidak boleh kosong.'
        ]);

        $teacher = Auth::user()->teacher;
        $schedule = Schedule::findOrFail($request->schedule_id);
        $now = now()->format('H:i:s');

        if ($now < $schedule->start_time) {
            return redirect()->back()->with('error', 'Sabar! Jam pelajaran belum dimulai (Mulai: ' . $schedule->start_time . ').');
        }

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
            try {
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');
                $journal->update(['photo_url' => $journal->getFirstMediaUrl('photo')]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memproses foto: ' . $e->getMessage());
            }
        }

        return redirect()->route('journal.index')->with('success', 'Jurnal berhasil disimpan!');
    }

    public function edit($id)
    {
        $journal = Journal::findOrFail($id);
        if ($journal->teacher_id != Auth::user()->teacher->id) {
            abort(403, 'Anda tidak memiliki akses ke jurnal ini.');
        }
        return view('frontend.journal.edit', compact('journal'));
    }

    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);

        if ($journal->teacher_id != Auth::user()->teacher->id) {
            abort(403);
        }

        $request->validate([
            'description' => 'required|min:5',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        $journal->description = $request->description;

        if ($request->hasFile('photo')) {
            try {
                $journal->clearMediaCollection('photo');
                $journal->addMediaFromRequest('photo')->toMediaCollection('photo');
                $journal->photo_url = $journal->getFirstMediaUrl('photo');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memperbarui foto: ' . $e->getMessage());
            }
        }

        $journal->save();
        return redirect()->route('journal.index')->with('success', 'Jurnal berhasil diperbarui!');
    }
}
