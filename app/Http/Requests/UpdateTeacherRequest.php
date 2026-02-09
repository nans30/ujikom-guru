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

    public function rules(): array
    {
        $teacherId = $this->route('teacher');

        return [

            /*
            |------------------------------------------------------------------
            | BASIC
            |------------------------------------------------------------------
            */
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'nip')->ignore($teacherId),
            ],

            'name' => 'required|string|max:150',

            /*
            |------------------------------------------------------------------
            | TAMBAHAN DATA GURU
            |------------------------------------------------------------------
            */
            'nuptk'         => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:150',
            'tanggal_lahir' => 'nullable|date',
            'nik'           => 'nullable|string|max:30',

            /*
            |------------------------------------------------------------------
            | AKUN LOGIN (USERS)
            |------------------------------------------------------------------
            */
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(
                    optional($this->teacher->user)->id
                ),
            ],

            'password' => 'nullable|string|min:6',

            /*
            |------------------------------------------------------------------
            | SYSTEM
            |------------------------------------------------------------------
            */
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

    public function attributes(): array
    {
        return [
            'nip'            => 'NIP',
            'name'           => 'Nama',
            'nuptk'          => 'NUPTK',
            'jenis_kelamin'  => 'Jenis Kelamin',
            'tempat_lahir'   => 'Tempat Lahir',
            'tanggal_lahir'  => 'Tanggal Lahir',
            'nik'            => 'NIK',
            'email'          => 'Email Login',
            'password'       => 'Password Login',
            'rfid_uid'       => 'RFID UID',
            'photo'          => 'Photo',
            'is_active'      => 'Status',
        ];
    }
}