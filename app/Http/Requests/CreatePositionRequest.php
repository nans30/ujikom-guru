<?php

namespace App\Http\Requests;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class CreatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules for creating a Position.
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