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
            'teacher_id' => 'required|exists:teachers,id',
            'date'       => 'required|date',

            'check_in'  => 'nullable|date',
            'check_out' => 'nullable|date|after:check_in',

            'method_in'  => 'nullable|in:rfid,manual',
            'method_out' => 'nullable|in:rfid,manual',

            'status' => 'required|in:hadir,telat,izin,sakit,alpha',

            // ✅ wajib check-in photo
            'photo_check_in' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // ✅ wajib kalau checkout ada
            'photo_check_out' => 'nullable_with:check_out|image|mimes:jpg,jpeg,png|max:2048',

            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'reason' => 'nullable|string|max:255',
            'late_duration' => 'nullable|integer|min:0',
        ];
    }
}