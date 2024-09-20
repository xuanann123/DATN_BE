<?php

namespace App\Http\Requests\Admin\Tags;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {

        //Lấy thằng tag này trên route
        $tagId = $this->route('tag'); // Lấy ID của thẻ từ route
        
        return [
            'name' => [
                'required', Rule::unique('tags')->ignore($tagId), // Bỏ qua kiểm tra unique cho thẻ đang được cập nhật
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên thẻ.',
            'name.unique' => 'Tên thẻ đã tồn tại.',
        ];
    }
}
