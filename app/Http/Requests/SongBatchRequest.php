<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SongBatchRequest extends FormRequest
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
            'ids' => 'required|array|min:1|max:50',
            'ids.*' => 'required|integer|exists:songs,id'
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Debe proporcionar al menos un ID',
            'ids.*.exists' => 'Una o más canciones no existen'
        ];
    }
}
