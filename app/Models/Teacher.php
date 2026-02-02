<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Teacher extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'teachers';

    protected $fillable = [
        // ======================
        // API DATA
        // ======================
        'nip',
        'name',
        'email',
        'nuptk',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'photo_url',

        // ======================
        // SYSTEM
        // ======================
        'password',
        'rfid_uid',
        'is_active',
        'created_by_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date', // biar otomatis carbon
    ];

    /**
     * Relasi ke user (admin yang membuat teacher)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Register media collection
     * 1 teacher = 1 photo (Spatie)
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('photo')
            ->singleFile();
    }

    /**
     * Helper ambil foto (prioritas Spatie → fallback API)
     */
    public function getPhotoAttribute()
    {
        return $this->getFirstMediaUrl('photo') ?: $this->photo_url;
    }
}