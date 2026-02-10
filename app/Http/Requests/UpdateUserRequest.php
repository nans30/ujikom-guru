<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('user')
            ? $this->route('user')->id
            : $this->id;

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'unique:users,email,' . $id . ',id,deleted_at,NULL',
            ],

            'confirm_email' => ['same:email'],

            'gender' => ['required'],

            'dob' => ['nullable', 'date'],
        ];
    }
}