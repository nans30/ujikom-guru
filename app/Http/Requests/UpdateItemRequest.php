<?php

namespace App\Http\Requests;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules untuk update data item.
     */
    public function rules(): array
    {
        return [
            'item_name'   => 'required|string|max:255',
            'point_cost'  => 'required|integer|min:1',
            'extra_minutes' => 'required|integer|min:0',
            'stock_limit' => 'nullable|integer|min:1',
            'status'      => 'required|in:0,1',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [
            'item_name'   => 'Nama Item',
            'point_cost'  => 'Harga Poin',
            'stock_limit' => 'Batas Stok',
            'image'       => 'Gambar Item',
            'extra_minutes' => 'Extra Menit',
        ];
    }
}
