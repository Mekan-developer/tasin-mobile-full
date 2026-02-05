<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/** Валидация обновления товара. */
class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|exists:categories,id',
            'active_image' => 'sometimes|integer|min:0',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'images' => 'sometimes|array',
            'images.*' => 'string|max:255',
            'views' => 'sometimes|integer|min:0',
            'is_new' => 'sometimes|boolean',
            'variants' => 'nullable|array',
            'variants.*.model' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'nullable|string|max:50',
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
