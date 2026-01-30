<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use SoftDeletes;

    protected $table = 'attendances';

    protected $fillable = [
        'teacher_id',
        'date',
        'check_in',
        'check_out',
        'method_in',
        'method_out',
        'photo_check_in',
        'photo_check_out',
        'proof_file',
        'status',
        'reason',
        'late_duration',
        'created_by_id',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers (optional tapi berguna)
    |--------------------------------------------------------------------------
    */

    public function isCheckedIn(): bool
    {
        return !is_null($this->check_in);
    }

    public function isCheckedOut(): bool
    {
        return !is_null($this->check_out);
    }
}