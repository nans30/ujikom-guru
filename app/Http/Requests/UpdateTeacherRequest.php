<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Helpers;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for updating teacher
     */
    public function rules(): array
    {
        $teacherId = $this->route('teacher');
        // pastikan route: /teacher/{teacher}

        return [
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'nip')->ignore($teacherId),
            ],

            'name'  => 'required|string|max:150',

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('teachers', 'email')->ignore($teacherId),
            ],

            'password' => 'nullable|string|min:6',

            'rfid_uid' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('teachers', 'rfid_uid')->ignore($teacherId),
            ],

            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),

            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Custom attribute names
     */
    public function attributes(): array
    {
        return [
            'nip'       => 'NIP',
            'name'      => 'Nama',
            'email'     => 'Email',
            'password'  => 'Password',
            'rfid_uid'  => 'RFID UID',
            'photo'     => 'Photo',
            'is_active' => 'Status',
        ];
    }
}