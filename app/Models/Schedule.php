<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'subject',
        'class_name',
        'day_of_week',
        'start_time',
        'end_time',
        'status',
        'created_by_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_time',
        'end_time',
    ];

    /**
     * Relasi ke guru
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke user yang membuat jadwal
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}