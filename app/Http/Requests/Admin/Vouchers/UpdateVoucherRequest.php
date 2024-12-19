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
            'discount' => 'required|numeric|min:0|max:30',
            'count' => 'required|integer|min:0',
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên voucher.',
            'name.max' => 'Tên voucher không được vượt quá 255 ký tự.',

            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.unique' => 'Mã voucher đã tồn tại.',
            'code.max' => 'Mã voucher không được vượt quá 255 ký tự.',

            'description.min' => 'Mô tả phải có ít nhất 6 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 65.535 ký tự.',

            'type.required' => 'Vui lòng chọn loại voucher.',

            'discount.required' => 'Vui lòng nhập số phần trăm/xu giảm.',
            'discount.numeric' => 'Số phần trăm/xu giảm phải là một số.',
            'discount.min' => 'Giảm giá phải ít nhất là 1.',
            'discount.max' => 'Giảm giá không được quá 30%.',

            'count.required' => 'Vui lòng nhập số lượng voucher.',
            'count.integer' => 'Số lượng phải là một số nguyên.',
            'count.min' => 'Số lượng phải ít nhất là 1.',

            'start_time.required' => 'Vui lòng nhập thời gian bắt đầu.',
            'start_time.date_format' => 'Thời gian bắt đầu phải theo định dạng Y-m-d\TH:i.',

            'end_time.required' => 'Vui lòng nhập thời gian kết thúc.',
            'end_time.date_format' => 'Thời gian kết thúc phải theo định dạng Y-m-d\TH:i.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',

        ];
    }
}
