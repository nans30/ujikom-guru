<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'status' => 'required|in:hadir,telat,izin,sakit,cuti,alpha',

            /*
            |--------------------------------------------------------------------------
            | HADIR / TELAT
            |--------------------------------------------------------------------------
            */
            'check_in'  => 'required_if:status,hadir,telat|nullable|date',
            'check_out' => 'nullable|date|after:check_in',

            'method_in'  => 'required_if:status,hadir,telat|nullable|in:rfid,manual',
            'method_out' => 'nullable|in:rfid,manual',

            'photo_check_in'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photo_check_out' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'late_duration' => 'required_if:status,telat|nullable|integer|min:0',

            /*
            |--------------------------------------------------------------------------
            | IZIN / SAKIT / CUTI
            |--------------------------------------------------------------------------
            */
            'proof_file' => 'required_if:status,izin,sakit,cuti|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'reason'     => 'required_if:status,izin,sakit,cuti|nullable|string|max:255',
        ];
    }
}