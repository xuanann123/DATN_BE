<?php

namespace App\Http\Requests\Client\Posts;

use App\Http\Requests\BaseFormRequest;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends BaseFormRequest
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
        $slug = $this->route('slug');
        $post = Post::where('slug', $slug)->first();

        $postId = $post ? $post->id : null;

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:6',
            'slug' => [
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId)
            ],
            'thumbnail' => 'nullable|image',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable',
        ];
    }
}
