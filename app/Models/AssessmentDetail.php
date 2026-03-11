<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentDetail extends Model
{
    protected $table = 'assessment_details';

    protected $fillable = [
        'assessment_id',
        'category_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Relasi balik ke Header Penilaian
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    /**
     * Relasi ke Kategori (Indikator Penilaian)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'category_id');
    }
}
