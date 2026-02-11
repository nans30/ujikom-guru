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

            // jenis (TAMBAH DINAS)
            'type' => 'required|in:izin,sakit,cuti,dinas',

            // alasan
            'reason' => 'required|string|max:255',

            // bukti
            'proof_file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
                function ($attr, $value, $fail) {
                    /**
                     * izin & sakit WAJIB bukti
                     * cuti & dinas TIDAK wajib
                     */
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
            'teacher_id.exists'   => 'Guru tidak valid.',

            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date'     => 'Format tanggal mulai tidak valid.',

            'end_date.required'        => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal'  => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',

            'type.required' => 'Jenis pengajuan wajib dipilih.',
            'type.in'       => 'Jenis pengajuan tidak valid.',

            'reason.required' => 'Alasan wajib diisi.',
            'reason.max'      => 'Alasan maksimal 255 karakter.',

            'proof_file.file'  => 'Bukti harus berupa file.',
            'proof_file.mimes' => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'proof_file.max'   => 'Ukuran bukti maksimal 2MB.',
        ];
    }
}