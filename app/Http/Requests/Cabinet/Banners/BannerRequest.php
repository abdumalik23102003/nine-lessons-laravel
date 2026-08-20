<?php

namespace App\Http\Requests\Cabinet\Banners;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'format' => ['nullable', 'string', 'max:50'],
            'file' => ['nullable', 'image', 'max:5000']
        ];
    }
}
