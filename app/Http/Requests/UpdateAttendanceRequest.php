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
        $rules = [
            'status' => 'required|in:hadir,telat,izin,sakit,cuti,alpha',
        ];

        /*
        |------------------------------------------------------------------
        | HADIR / TELAT
        |------------------------------------------------------------------
        */
        if (in_array($this->status, ['hadir', 'telat'])) {
            $rules += [
                'check_in'      => 'required|date',
                'check_out'     => 'nullable|date|after:check_in',
                'method_in'     => 'required|in:rfid,manual',
                'method_out'    => 'nullable|in:rfid,manual',

                // 🔥 PHOTO TIDAK WAJIB
                'photo_check_in'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'photo_check_out' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                'late_duration' => $this->status === 'telat'
                    ? 'required|integer|min:0'
                    : 'nullable',
            ];
        }

        /*
        |------------------------------------------------------------------
        | IZIN / SAKIT / CUTI
        |------------------------------------------------------------------
        */
        if (in_array($this->status, ['izin', 'sakit', 'cuti'])) {
            $rules += [
                'reason'     => 'required|string|max:255',
                'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];
        }

        return $rules;
    }
}