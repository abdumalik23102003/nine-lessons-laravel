<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism talab qilinadi.',
            'name.string' => 'Ism satr bo\'lishi kerak.',
            'name.max' => 'Ism maksimal 255 ta belgidan ko\'p bo\'lmasligi kerak.',
            'email.required' => 'Email talab qilinadi.',
            'email.email' => 'Email noto\'g\'ri formatda.',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan.',
        ];
    }
}
