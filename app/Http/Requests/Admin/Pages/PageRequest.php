<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if (!$this->filled('menu_title')) {
            $data['menu_title'] = $this->input('title');
        }

        if (!$this->filled('slug')) {
            $data['slug'] = Str::slug($this->input('title'));
        }

        $data['show_in_menu'] = $this->boolean('show_in_menu');

        $this->merge($data);
    }

    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'menu_title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($pageId),
            ],
            'content' => ['required', 'string'],
            'show_in_menu' => ['boolean'],
        ];
    }
}
