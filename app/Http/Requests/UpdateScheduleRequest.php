<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id'   => 'required|exists:teachers,id',
            'subject'      => 'required|string|max:255',
            'class_name'   => 'nullable|string|max:255',
            'day_of_week'  => 'required|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'status'       => 'required|in:0,1',
        ];
    }
}