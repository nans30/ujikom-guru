<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Data
            |--------------------------------------------------------------------------
            */
            'teacher_id' => 'required|exists:teachers,id',
            'date'       => 'required|date',

            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */
            'check_in'  => 'nullable|date',
            'check_out' => 'nullable|date|after:check_in',

            /*
            |--------------------------------------------------------------------------
            | Method
            |--------------------------------------------------------------------------
            */
            'method_in'  => 'nullable|in:rfid,manual',
            'method_out' => 'nullable|in:rfid,manual',

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            'status' => 'required|in:hadir,telat,izin,sakit,alpha',

            /*
            |--------------------------------------------------------------------------
            | PHOTO (wajib kalau hadir/telat)
            |--------------------------------------------------------------------------
            */
            'photo_check_in' => [
                'required_if:status,hadir,telat',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

            'photo_check_out' => [
                'required_if:status,hadir,telat',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

            /*
            |--------------------------------------------------------------------------
            | PROOF (wajib kalau izin/sakit)
            |--------------------------------------------------------------------------
            */
            'proof_file' => [
                'required_if:status,izin,sakit',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048'
            ],

            /*
            |--------------------------------------------------------------------------
            | Others
            |--------------------------------------------------------------------------
            */
            'reason' => 'nullable|string|max:255',
            'late_duration' => 'nullable|integer|min:0',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Messages (biar user friendly)
    |--------------------------------------------------------------------------
    */
    public function messages(): array
    {
        return [
            'photo_check_in.required_if'  => 'Foto check-in wajib untuk status hadir/telat.',
            'photo_check_out.required_if' => 'Foto check-out wajib untuk status hadir/telat.',
            'proof_file.required_if'      => 'Bukti file wajib untuk izin/sakit.',
            'check_out.after'             => 'Waktu check-out harus setelah check-in.',
        ];
    }
}