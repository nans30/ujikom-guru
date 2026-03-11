<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evaluatee_id'    => 'required|exists:teachers,id',
            'assessment_date' => 'required|date',

            // Update: Mengikuti struktur kolom baru
            'semester'        => 'required|in:1,2',
            'academic_year'   => 'required|string|regex:/^\d{4}\/\d{4}$/',

            'general_notes'   => 'nullable|string',
            'status'          => 'required|in:1,2',

            'scores'          => 'required|array',
            'scores.*'        => 'required|integer|min:1|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'evaluatee_id.required'  => 'Pilih guru yang akan dinilai.',
            'semester.required'      => 'Semester harus dipilih.',
            'academic_year.required' => 'Tahun ajaran harus diisi.',
            'academic_year.regex'    => 'Format tahun ajaran salah (contoh: 2025/2026).',
            'scores.required'        => 'Anda harus memberikan nilai pada indikator.',
            'scores.*.min'           => 'Nilai minimal adalah 1 bintang.',
            'scores.*.max'           => 'Nilai maksimal adalah 5 bintang.',
        ];
    }
}
