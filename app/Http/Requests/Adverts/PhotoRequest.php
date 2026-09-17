<?php

namespace App\Http\Requests\Adverts;

use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'required|image|mimes:jpeg,png,webp|max:5120', // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Rasimlarni yuklang.',
            'photos.array' => 'Rasimlar array bo\'lishi kerak.',
            'photos.min' => 'Kamida 1 ta rasm yuklang.',
            'photos.max' => 'Maksimal 10 ta rasm yuklash mumkin.',
            'photos.*.required' => 'Rasm talab qilinadi.',
            'photos.*.image' => 'Fayl rasm bo\'lishi kerak.',
            'photos.*.mimes' => 'Rasm JPEG, PNG yoki WEBP formatida bo\'lishi kerak.',
            'photos.*.max' => 'Har bir rasm maksimal 5MB bo\'lishi kerak.',
        ];
    }
}
