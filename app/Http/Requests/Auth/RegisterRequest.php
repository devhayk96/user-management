<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => 'required|alpha|min:2|max:255',
            'lastName'  => 'required|alpha|min:2|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|max:60|confirmed',
            'deviceName'=> 'nullable|alpha',
        ];
    }
}
