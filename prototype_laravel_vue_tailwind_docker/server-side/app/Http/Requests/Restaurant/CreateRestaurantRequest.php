<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateRestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only super-admin can create restaurants
        return auth()->check() && auth()->user()->hasRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'restaurant.name' => 'required|string|max:255',
            'restaurant.description' => 'nullable|string',
            'restaurant.address' => 'required|string|max:255',
            'restaurant.phone' => 'nullable|string|max:20',
            'restaurant.active' => 'sometimes|boolean',

            'owner.name' => 'required|string|max:255',
            'owner.email' => 'required|email|unique:users,email',
            'owner.password' => 'required|string|min:8',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'restaurant.name.required' => 'Название ресторана обязательно',
            'restaurant.address.required' => 'Адрес ресторана обязателен',
            'owner.name.required' => 'Имя владельца обязательно',
            'owner.email.required' => 'Email владельца обязателен',
            'owner.email.unique' => 'Этот email уже используется',
            'owner.password.required' => 'Пароль обязателен',
            'owner.password.min' => 'Пароль должен содержать минимум 8 символов',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}

