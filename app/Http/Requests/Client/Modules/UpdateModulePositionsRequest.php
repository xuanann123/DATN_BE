<?php

namespace App\Http\Requests\Client\Modules;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModulePositionsRequest extends FormRequest
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
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:modules,id',
            'modules.*.position' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'modules.array' => 'Dữ liệu phải là một mảng.',
            'modules.*.id.required' => 'ID bài học là bắt buộc.',
            'modules.*.id.exists' => 'Bài học không tồn tại.',
            'modules.*.position.required' => 'Vị trí là bắt buộc.',
            'modules.*.position.integer' => 'Vị trí phải là một số nguyên.',
        ];
    }
}
