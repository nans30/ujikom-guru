<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Teacher extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'nuptk',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'photo_url',
        'rfid_uid',
        'is_active',
        'created_by_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    /** akun login */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** admin pembuat */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function getPhotoAttribute()
    {
        return $this->getFirstMediaUrl('photo') ?: $this->photo_url;
    }
}