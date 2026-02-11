<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use SoftDeletes;

    protected $table = 'approvals';

    protected $fillable = [
        'teacher_id',
        'type',        // izin | sakit | cuti | dinas
        'start_date',
        'end_date',
        'reason',
        'proof_file',
        'status',      // pending | approved | rejected
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------
    | RELATIONS
    |--------------------------------------------------
    */

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------
    | HELPERS
    |--------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDinas(): bool
    {
        return $this->type === 'dinas';
    }
}