<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    | Dipakai hanya jika admin mengubah data (jarang dipakai)
    */
    public function rules(): array
    {
        return [

            // hanya boleh ubah status
            'status' => 'required|in:approved,rejected',

        ];
    }
}