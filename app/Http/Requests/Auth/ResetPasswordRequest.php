<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email|exists:users',
            'password' => 'required|min:8|max:60|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'There is no any user with selected email.'
        ];
    }
}
