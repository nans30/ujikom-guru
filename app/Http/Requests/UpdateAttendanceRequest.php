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
            'check_in'  => 'nullable|date',
            'check_out' => 'nullable|date|after:check_in',

            'method_in'  => 'nullable|in:rfid,manual',
            'method_out' => 'nullable|in:rfid,manual',

            'status' => 'required|in:hadir,telat,izin,sakit,alpha',

            // update → optional
            'photo_check_in'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photo_check_out' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'reason' => 'nullable|string|max:255',
            'late_duration' => 'nullable|integer|min:0',
        ];
    }
}