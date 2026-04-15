<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointLedger extends Model
{
    protected $table = 'point_ledgers';

    protected $fillable = [
        'teacher_id',
        'transaction_type', // EARN, SPEND, PENALTY
        'amount',           // Jumlah poin yang masuk/keluar
        'current_balance',  // Saldo terakhir saat transaksi terjadi
        'description'       // Keterangan (Cth: "Absen Tepat Waktu")
    ];

    /** Relasi ke Guru pemilik poin */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
