<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // guru
            'teacher_id' => 'required|exists:teachers,id',

            // rentang tanggal
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',

            // jenis
            'type' => 'required|in:izin,sakit,cuti',

            // alasan
            'reason' => 'required|string|max:255',

            // bukti (izin & sakit wajib, cuti optional)
            'proof_file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
                function ($attr, $value, $fail) {
                    if (in_array($this->type, ['izin', 'sakit']) && !$value) {
                        $fail('Bukti wajib untuk izin atau sakit.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.required' => 'Guru wajib dipilih.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'type.required' => 'Jenis pengajuan wajib dipilih.',
        ];
    }
}