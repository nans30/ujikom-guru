<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    | Dipakai saat guru submit izin/sakit
    */
    public function rules(): array
    {
        return [

            // guru
            'teacher_id' => 'required|exists:teachers,id',

            // tanggal izin
            'date' => 'required|date',

            // jenis izin
            'type' => 'required|in:izin,sakit',

            // alasan
            'reason' => 'required|string|max:255',

            // bukti wajib
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom message biar enak dibaca user
    |--------------------------------------------------------------------------
    */
    public function messages(): array
    {
        return [
            'proof_file.required' => 'Bukti wajib diupload',
            'teacher_id.required' => 'Guru wajib dipilih',
        ];
    }
}