<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PointLedger;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    /**
     * Menampilkan riwayat poin (ledger) milik guru yang sedang login
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Ambil semua transaksi untuk ringkasan
        $allLedgers = PointLedger::where('teacher_id', $teacher->id)->get();
        
        $summary = [
            'earned'  => $allLedgers->where('transaction_type', 'EARN')->sum('amount'),
            'spent'   => abs($allLedgers->where('transaction_type', 'SPEND')->sum('amount')),
            'penalty' => abs($allLedgers->where('transaction_type', 'PENALTY')->sum('amount')),
        ];

        $ledgers = PointLedger::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('frontend.points.index', compact('ledgers', 'teacher', 'summary'));
    }

    /**
     * Menampilkan papan peringkat (leaderboard) berdasarkan saldo poin terbanyak
     */
    public function leaderboard()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $rankings = Teacher::with('position')
            ->orderBy('point_balance', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('frontend.points.leaderboard', compact('rankings', 'teacher'));
    }
}
