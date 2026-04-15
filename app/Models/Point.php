<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Point extends Model
{
    use SoftDeletes;

    protected $table = 'points'; // Menunjuk ke tabel 'points'

    protected $fillable = [
        'name',
        'condition_operator',
        'condition_value',
        'point_modifier',
        'status',
        'created_by_id'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
