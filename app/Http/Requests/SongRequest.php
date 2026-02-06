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
            'lyrics_blocks' => [$isUpdate ? 'sometimes' : 'required', 'array', 'min:1'],
            'lyrics_blocks.*.id' => 'nullable',
            'lyrics_blocks.*.type' => 'required|in:verse,chorus,bridge',
            'lyrics_blocks.*.content' => 'required|string',
            'lyrics_blocks.*.label' => 'nullable|string',
            'key' => 'nullable|string|max:150',
            'rhythm' => 'nullable|string|max:150',
            'tempo' => 'nullable|integer|min:20|max:240',
            'video_url' => 'nullable|url|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
