<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\Helpers;

class CreateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating teacher
     */
    public function rules(): array
    {
        return [
            'nip'       => 'required|string|max:50|unique:teachers,nip',
            'name'      => 'required|string|max:150',
            'email'     => 'nullable|email|max:255|unique:teachers,email',
            'password'  => 'nullable|string|min:6',
            'rfid_uid'  => 'nullable|string|max:100|unique:teachers,rfid_uid',

            'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),

            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Custom attribute names (biar error lebih manusiawi)
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