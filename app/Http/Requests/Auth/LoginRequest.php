<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'There is no any user with selected email.'
        ];
    }
}
