<?php

namespace App\Http\Requests\Admin\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoucherRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        $id = $this->route('voucher');
        return [
            'name' => 'required|max:255',
            'code' => 'required|unique:vouchers,code,' . $id . '|max:255',
            'description' => 'nullable|min:6|max:65535',
            'type' => 'required',
            'discount' => 'required|numeric|min:0',
            'count' => 'required|integer|min:0',
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'code.required' => 'The code field is required.',
            'code.unique' => 'The code already exists.',
            'code.max' => 'The code may not be greater than 255 characters.',

            'description.min' => 'The description must be at least 6 characters.',
            'description.max' => 'The description may not be greater than 65,535 characters.',

            'type.required' => 'The type field is required.',

            'discount.required' => 'The discount field is required.',
            'discount.numeric' => 'The discount must be a number.',
            'discount.min' => 'The discount must be at least 0.',

            'count.required' => 'The count field is required.',
            'count.integer' => 'The count must be an integer.',
            'count.min' => 'The count must be at least 0.',

            'start_time.required' => 'The start time field is required.',
            'start_time.date_format' => 'The start time must be in the format Y-m-d\TH:i.',

            'end_time.required' => 'The end time field is required.',
            'end_time.date_format' => 'The end time must be in the format Y-m-d\TH:i.',
            'end_time.after' => 'The end time must be after the start time.',
        ];
    }
}
