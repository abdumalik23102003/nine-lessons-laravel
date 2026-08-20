<?php

namespace App\Http\Requests\Admin\Categories;

use App\Models\Category;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;
        $parentId = $this->integer('parent_id') ?: null;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')->where('parent_id', $parentId)->ignore($categoryId),
            ],
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('categories', 'slug')->where('parent_id', $parentId)->ignore($categoryId),
            ],
            'parent_id' => [
                'nullable', 'integer', Rule::exists('categories', 'id')
            ],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $categoryId = $this->route('category')?->id;
            $parentId = $this->integer('parent_id') ?: null;

            if (!$categoryId || !$parentId) {
                return;
            }

            if ($parentId === $categoryId) {
                $validator->errors()->add('parent_id', "Kategoriya o'zining ota-kategoriyasi bo'la olmaydi.");

                return;
            }

            if (in_array($parentId, $this->descendantIds($categoryId), true)) {
                $validator->errors()->add('parent_id', "Kategoriya o`z farzand kategoriyasiga ko`chirilishi mumkin emas.");
            }
        });
    }

    private function descendantIds(int $categoryId): array
    {
        $ids = [];

        $collect = function (int $id) use (&$collect, &$ids) {
            foreach (Category::query()->where('parent_id', $id)->pluck('id') as $childId) {
                $ids[] = $childId;
                $collect($childId);
            }
        };

        $collect($categoryId);
        return $ids;
    }
}
