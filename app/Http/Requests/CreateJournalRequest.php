<?php

namespace App\Http\Requests;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class CreateJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id'   => 'required|exists:teachers,id',
            'schedule_id'  => 'required|exists:schedules,id',
         
            'description'  => 'nullable|string|max:1000',
            'status'       => 'required|in:0,1',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:' . Helpers::getMaxUploadFileSize(),
        ];
    }
}