<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'title' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'artist' => 'nullable|string|max:255',
            'lyrics' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'key' => 'nullable|string|max:10',
            'rhythm' => 'nullable|string|max:50',
            'tempo' => 'nullable|string|max:50',
            'video_url' => 'nullable|url|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
