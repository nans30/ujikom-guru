<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Journal extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'teacher_id',
        'schedule_id',
        'date',
        'description',
        'photo_url',
        'status',
        'created_by_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relasi ke guru (teacher)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke jadwal pelajaran (teacher schedule)
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Accessor untuk mendapatkan URL foto secara dinamis
     * Mengutamakan Spatie Media Library, fallback ke kolom photo_url (asli)
     */
    public function getPhotoUrlAttribute(): ?string
    {
        $media = $this->getFirstMediaUrl('photo');
        if ($media) {
            return $media;
        }

        // Jika ada data di kolom photo_url tapi formatnya URL lama (pake IP), 
        // kita bersihkan agar relatif atau sesuai APP_URL baru jika memungkinkan.
        // Namun Spatie Media biasanya sudah menangani URL ke public storage.
        return $this->attributes['photo_url'] ?? null;
    }

    /**
     * Relasi ke user yang input jurnal
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
