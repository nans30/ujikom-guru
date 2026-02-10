<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Approval;
use App\Models\Teacher;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |---------------------------------------------------------
    | FORM IZIN / SAKIT / CUTI + RIWAYAT APPROVAL
    |---------------------------------------------------------
    */
    public function index()
    {
        // ambil teacher dari user login
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        // ambil semua pengajuan izin guru ini
        $approvals = Approval::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.permission.index', compact('approvals'));
    }

    /*
    |---------------------------------------------------------
    | STORE → APPROVAL (STATUS: PENDING)
    |---------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|in:izin,sakit,cuti',
            'reason'     => 'required|string|max:255',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')
                ->store('approval/proofs', 'public');
        }

        Approval::create([
            'teacher_id'    => $teacher->id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'type'          => $request->type,      // izin | sakit | cuti
            'reason'        => $request->reason,
            'proof_file'    => $proofPath,
            'status'        => 'pending',
            'created_by_id' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pengajuan berhasil dikirim dan menunggu approval admin.');
    }
}