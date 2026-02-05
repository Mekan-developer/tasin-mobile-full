<?php

namespace App\Http\Requests\Slide;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/** Валидация обновления слайда. */
class UpdateSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'badge' => 'nullable|string|max:255',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'bg_color' => 'sometimes|string|max:20',
            'image' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'old_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|string|max:20',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
