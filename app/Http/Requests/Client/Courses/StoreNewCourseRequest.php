<?php

namespace App\Http\Requests\Client\Courses;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewCourseRequest extends BaseFormRequest
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
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'slug' => 'nullable|string|unique:courses,slug|max:255',
        ];
    }
}
