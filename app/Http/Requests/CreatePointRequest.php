<?php

namespace App\Http\Requests;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class CreatePointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'condition_operator' => 'required|in:<,>,BETWEEN',
            'condition_value'    => 'required|string', // Bisa berupa jam (07:00) atau angka menit
            'point_modifier'     => 'required|integer', // Bisa negatif (misal: -5) atau positif (10)
            'status'             => 'required|in:0,1',
            'image'              => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),
        ];
    }

    /**
     * Custom attributes untuk pesan error yang lebih rapi
     */
    public function attributes(): array
    {
        return [
            'condition_operator' => 'Operator Kondisi',
            'condition_value'    => 'Nilai Kondisi',
            'point_modifier'     => 'Jumlah Poin',
        ];
    }
}
