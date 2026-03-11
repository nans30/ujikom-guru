<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_date',
        'semester',
        'academic_year',
        'general_notes',
        'status',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'status'          => 'integer',
    ];

    /**
     * Accessor: Gabungan Semester & Tahun Ajaran
     */
    public function getPeriodAttribute(): string
    {
        $semesterLabel = $this->semester == '1' ? 'Ganjil' : 'Genap';
        return "Semester {$this->semester} ({$semesterLabel}) - {$this->academic_year}";
    }

    /**
     * Accessor: Total Skor (Sum)
     */
    public function getTotalScoreAttribute(): int
    {
        return $this->details->sum('score');
    }

    /**
     * Accessor: Rata-rata Skor (Avg)
     */
    public function getAverageScoreAttribute(): float
    {
        $count = $this->details->count();
        return $count > 0 ? round($this->details->avg('score'), 2) : 0;
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatee(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'evaluatee_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class, 'assessment_id');
    }
}
