<?php

namespace App\Http\Requests\Client\Posts;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_user' => 'required|integer|exists:users,id',
            'content' => 'required|string|min:2|max:255',
            'commentable_id' => 'required|integer|exists:posts,id',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Vui lòng thêm id người dùng.',
            'id_user.integer' => 'Id người dùng là số nguyên.',
            'id_user.exists' => 'Người dùng không tồn tại',
            'content.required' => 'Vui lòng nhập bình luận',
            'content.string' => 'Bình luận phải là chuỗi.',
            'content.min' => 'Bình luận phải trên 2 kí tự.',
            'content.max' => 'Bình luận không quá 255 kí tự.',
            'commentable_id.required' => 'Vui lòng thêm id bài viết.',
            'commentable_id.integer' => 'Id bài viết là số nguyên.',
            'commentable_id.exists' => 'Bài viết không tồn tại',
            'parent_id.integer' => 'Id bình luận là số nguyên.',
            'parent_id.exists' => 'Bình luận không tồn tại',
        ];
    }
}
