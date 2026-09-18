<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestPhoneVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[1-9]\d{1,14}$/', // E.164 format
                Rule::unique('users', 'phone')
                    ->ignore($this->user()?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be in international format (e.g., +998901234567)',
            'phone.unique' => 'This phone number is already registered',
        ];
    }
}
