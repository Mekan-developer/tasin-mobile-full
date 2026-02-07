<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/** Валидация создания категории (FormData: show_price как строка, image_icon — файл или строка). */
class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Нормализует show_price и is_active из FormData ("true"/"1" → true). */
    protected function prepareForValidation(): void
    {
        if ($this->has('show_price')) {
            $v = $this->show_price;
            $this->merge(['show_price' => filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $v]);
        }
        if ($this->has('is_active')) {
            $v = $this->is_active;
            $this->merge(['is_active' => filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $v]);
        }
    }

    public function rules(): array
    {
        $imageRule = $this->hasFile('image_icon')
            ? ['nullable', 'file', 'image', 'max:2048']
            : ['nullable', 'string', 'max:255'];

        return [
            'name' => 'required|string|max:255',
            'order' => 'sometimes|integer|min:0',
            'show_price' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'image_icon' => $imageRule,
        ];
    }

    public function messages(): array
    {
        return [
            'image_icon.max' => 'Изображение не должно превышать 2 МБ.',
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
