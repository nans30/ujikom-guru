<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use SoftDeletes;

    // Nama tabel secara default adalah jamak dari nama model (items)
    protected $table = 'items';

    protected $fillable = [
        'item_name',
        'point_cost',
        'extra_minutes',
        'stock_limit',
        'status',
        'created_by_id'
    ];

    /**
     * Secara otomatis menangani casting tipe data
     */
    protected $casts = [
        'point_cost' => 'integer',
        'stock_limit' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke user yang membuat item ini
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Scope untuk mengambil hanya item yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
