<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validasi: Pastikan ID ada di tabel teachers
            'evaluatee_id'    => 'required|exists:teachers,id',
            'assessment_date' => 'required|date',

            // Update: Mengikuti struktur kolom baru
            'semester'        => 'required|in:1,2',
            'academic_year'   => 'required|string|regex:/^\d{4}\/\d{4}$/', // Format: 2025/2026

            'general_notes'   => 'nullable|string',
            'status'          => 'required|in:1,2', // 1: Draft, 2: Final

            // Validasi Detail Nilai (Array dari Star Rating)
            'scores'          => 'required|array',
            'scores.*'        => 'required|integer|min:1|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'evaluatee_id.required'    => 'Pilih guru yang akan dinilai.',
            'evaluatee_id.exists'      => 'Data guru tidak ditemukan dalam sistem.',
            'semester.required'        => 'Pilih semester penilaian.',
            'academic_year.required'   => 'Tahun ajaran wajib diisi.',
            'academic_year.regex'      => 'Format tahun ajaran harus XXXX/XXXX (contoh: 2025/2026).',
            'scores.required'          => 'Anda harus memberikan nilai pada indikator penilaian.',
            'scores.*.required'        => 'Wajib mengisi nilai bintang.',
            'scores.*.min'             => 'Nilai minimal adalah 1 bintang.',
            'scores.*.max'             => 'Nilai maksimal adalah 5 bintang.',
            'assessment_date.required' => 'Tanggal penilaian wajib diisi.',
        ];
    }
}
