<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/** Валидация обновления товара (JSON или FormData с images[]). */
class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Нормализует is_new и images из FormData. */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_new')) {
            $v = $this->is_new;
            $this->merge(['is_new' => filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $v]);
        }
        if ($this->has('existing_images') && ! $this->has('images')) {
            $decoded = json_decode($this->existing_images, true);
            $this->merge(['images' => is_array($decoded) ? $decoded : []]);
        }
    }

    public function rules(): array
    {
        $imagesItemRule = $this->hasFile('images')
            ? ['file', 'image', 'max:2048']
            : ['string', 'max:255'];

        return [
            'category_id' => 'sometimes|exists:categories,id',
            'active_image' => 'sometimes|integer|min:0',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'images' => 'sometimes|array',
            'images.*' => $imagesItemRule,
            'existing_images' => 'sometimes|string',
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
