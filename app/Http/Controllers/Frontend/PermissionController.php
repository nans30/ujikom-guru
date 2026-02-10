<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Approval;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('frontend.permission.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_from'  => 'required|date',
            'date_to'    => 'required|date|after_or_equal:date_from',
            'type'       => 'required|string',
            'reason'     => 'required|string',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('attendance/proofs', 'public');
        }

        Approval::create([
            'teacher_id'    => Auth::id(),
            'date_from'     => $request->date_from,
            'date_to'       => $request->date_to,
            'type'          => $request->type,
            'reason'        => $request->reason,
            'proof_file'    => $proofPath,
            'status'        => 'pending',
            'created_by_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Permission request submitted and awaiting approval.');
    }
}