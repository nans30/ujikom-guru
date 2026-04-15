<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherToken extends Model
{
    protected $table = 'teacher_tokens';

    protected $fillable = [
        'teacher_id',
        'item_id',
        'status',                   // AVAILABLE, USED, EXPIRED
        'used_at_attendance_id',    // ID absensi saat token ini dipakai
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /** Relasi ke Guru pemilik token */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /** Relasi ke Detail Item (untuk tahu ini token apa, misal: Token Telat) */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /** Relasi ke Absensi (untuk tahu token ini dipakai di hari apa) */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'used_at_attendance_id');
    }
}
