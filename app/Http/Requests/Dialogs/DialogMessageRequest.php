<?php

namespace App\Http\Requests\Dialogs;

use Illuminate\Foundation\Http\FormRequest;

class DialogMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
