<?php

namespace App\Http\Requests;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules untuk pembuatan item baru.
     */
    public function rules(): array
    {
        return [
            'item_name'   => 'required|string|max:255',
            'extra_minutes' => 'required|integer|min:0',
            'point_cost'  => 'required|integer|min:1',
            'stock_limit' => 'nullable|integer|min:1',
            'status'      => 'required|in:0,1',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),
        ];
    }

    /**
     * Custom attributes agar pesan error lebih enak dibaca.
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
