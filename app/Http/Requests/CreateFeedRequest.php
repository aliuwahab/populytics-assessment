<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateFeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The feed name is required.',
            'name.string' => 'The feed name must be a string.',
            'name.max' => 'The feed name may not be greater than 255 characters.',
            'url.required' => 'The feed URL is required.',
            'url.url' => 'The feed URL must be a valid URL.',
            'url.max' => 'The feed URL may not be greater than 255 characters.',
        ];
    }
}
