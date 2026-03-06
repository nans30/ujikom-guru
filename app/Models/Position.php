<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'created_by_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relasi ke user yang membuat posisi
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Relasi ke teacher yang memiliki posisi ini
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class, 'position_id');
    }
}