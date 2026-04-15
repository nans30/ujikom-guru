<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Teacher;
use App\Models\TeacherToken;
use App\Models\PointLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    /**
     * Menampilkan halaman daftar item yang bisa ditukar.
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Hanya mengambil item yang statusnya aktif
        $items = Item::where('status', 1)->get();

        return view('frontend.shop.index', compact('teacher', 'items'));
    }

    /**
     * Menampilkan halaman inventory (item milik guru).
     */
    public function inventory()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Ambil token guru (item yang sudah dibeli)
        $tokens = TeacherToken::with('item')
            ->where('teacher_id', $teacher->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.shop.inventory', compact('teacher', 'tokens'));
    }

    /**
     * Proses menukar (redeem) poin dengan item.
     */
    public function redeem(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $quantity = (int) $request->quantity;
        $totalCost = $item->point_cost * $quantity;

        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return back()->with('error', 'Data profil guru tidak ditemukan.');
        }

        // 1. Cek apakah poin cukup
        if ($teacher->point_balance < $totalCost) {
            return back()->with('error', 'Poin Anda tidak mencukupi untuk membeli ' . $quantity . ' ' . $item->item_name . '.');
        }

        // 2. Transaksi Database
        DB::beginTransaction();
        try {
            $teacher = Teacher::where('id', $teacher->id)->lockForUpdate()->first();

            if ($teacher->point_balance < $totalCost) {
                DB::rollBack();
                return back()->with('error', 'Poin Anda tidak mencukupi.');
            }

            // Kurangi poin total
            $teacher->point_balance -= $totalCost;
            $teacher->save();

            // Buat Token Guru sebanyak jumlah yang dibeli
            for ($i = 0; $i < $quantity; $i++) {
                TeacherToken::create([
                    'teacher_id' => $teacher->id,
                    'item_id'    => $item->id,
                    'status'     => 'AVAILABLE',
                ]);
            }

            // Catat Ledger Sejarah (Satu entri untuk total transaksi)
            PointLedger::create([
                'teacher_id'       => $teacher->id,
                'transaction_type' => 'PENALTY',
                'amount'           => -$totalCost,
                'current_balance'  => $teacher->point_balance,
                'description'      => 'Tukar Poin (' . $quantity . 'x): ' . $item->item_name,
            ]);

            DB::commit();
            return back()->with('success', 'Berhasil menukar ' . $totalCost . ' poin dengan ' . $quantity . ' ' . $item->item_name . '!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
