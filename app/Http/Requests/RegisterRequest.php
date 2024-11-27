<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|regex:/^01[0125][0-9]{8}$/',
        ];
    }
 public function messages(): array
 {
    return [
        'name.required' => 'The name is a required field.',
    'name.string' => 'The name must be a string.',
    'name.max' => 'The name must not exceed 255 characters.',

    'email.required' => 'The email is a required field.',
    'email.email' => 'Please enter a valid email address.',
    'email.unique' => 'The email you entered is already in the database.',

    'password.required' => 'The password is a required field.',
    'password.string' => 'The password must be a string.',
    'password.min' => 'The password must be at least 6 characters.',
    'password.confirmed' => 'The password and password confirmation do not match.',

    'phone.required' => 'The phone number is a required field.',
    ];
 }
}

