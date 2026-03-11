<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * user yang membuat kategori
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * relasi ke assessment details
     */
    public function assessmentDetails(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class, 'category_id');
    }

    /**
     * helper status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}
