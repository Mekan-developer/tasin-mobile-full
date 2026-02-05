<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/** Валидация создания товара. */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'active_image' => 'sometimes|integer|min:0',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'images' => 'required|array',
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
