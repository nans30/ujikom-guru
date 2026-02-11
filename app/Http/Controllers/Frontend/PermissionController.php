<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Approval;
use App\Models\Teacher;
use Carbon\Carbon;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX → FORM & RIWAYAT PENGAJUAN
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $approvals = Approval::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.permission.index', compact('approvals'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE → BUAT PENGAJUAN BARU
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|in:izin,sakit,cuti,dinas',
            'reason'     => 'required|string|max:255',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'type.in' => 'Jenis pengajuan tidak valid. Pilih izin, sakit, cuti, atau dinas.',
        ]);

        $teacher   = Teacher::where('user_id', Auth::id())->firstOrFail();
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $today     = Carbon::today();

        // Cek apakah start_date sebelum hari ini (hanya admin bisa)
        if ($startDate->lt($today) && !$teacher->user->is_admin) {
            return redirect()->back()->withErrors('Tidak bisa memilih tanggal sebelum hari ini.');
        }

        // Cek overlapping pengajuan PENDING & APPROVED
        $existing = Approval::where('teacher_id', $teacher->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->exists();

        if ($existing) {
            return redirect()->back()
                ->withErrors('Tidak bisa membuat pengajuan. Ada tanggal yang sudah diajukan sebelumnya dan belum di-reject.');
        }

        // Simpan file bukti jika ada
        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('approval/proofs', 'public');
        }

        // Buat pengajuan baru
        Approval::create([
            'teacher_id'    => $teacher->id,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'type'          => $request->type,
            'reason'        => $request->reason,
            'proof_file'    => $proofPath,
            'status'        => 'pending',
            'created_by_id' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Pengajuan berhasil dikirim dan menunggu approval admin.');
    }
}