<?php

namespace App\Http\Requests;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules for updating a Position.
     */
    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),
        ];
    }
}