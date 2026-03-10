<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Library Intervension Image v3
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

    public function create()
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

        return view('frontend.journal.create', compact('schedules', 'completedScheduleIds'));
    }

    public function store(Request $request)
    {
        // Validasi: naikkan max size ke 5MB karena akan kita kompres di server
        $request->validate([
            'schedule_id' => 'required',
            'description' => 'required|min:5',
            'photo'       => 'required|image|mimes:jpeg,png,jpg|max:5120',
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
                // Inisialisasi Manager v3 dengan Driver GD
                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('photo'));

                // Resize ke lebar 1000px, tinggi otomatis (scale adalah fitur v3)
                $image->scale(width: 1000);

                // Path sementara untuk menyimpan hasil kompresi
                $tempPath = storage_path('app/public/temp_' . time() . '.jpg');

                // Simpan sebagai JPEG dengan kualitas 70%
                $image->toJpeg(70)->save($tempPath);

                // Masukkan ke Media Library dari path temporary
                $journal->addMedia($tempPath)->toMediaCollection('photo');

                // Hapus file temporary
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

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
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $journal->description = $request->description;

        if ($request->hasFile('photo')) {
            try {
                $journal->clearMediaCollection('photo');

                // PROSES KOMPRES VERSI 3
                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('photo'));

                $image->scale(width: 1000);

                $tempPath = storage_path('app/public/temp_' . time() . '.jpg');
                $image->toJpeg(70)->save($tempPath);

                $journal->addMedia($tempPath)->toMediaCollection('photo');

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                $journal->photo_url = $journal->getFirstMediaUrl('photo');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memperbarui foto: ' . $e->getMessage());
            }
        }

        $journal->save();
        return redirect()->route('journal.index')->with('success', 'Jurnal berhasil diperbarui!');
    }
}
