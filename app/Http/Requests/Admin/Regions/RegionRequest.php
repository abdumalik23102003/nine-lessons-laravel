<?php

namespace App\Http\Requests\Admin\Regions;

use App\Models\Region;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $regionId = $this->route('region')?->id;
        $parentId = $this->integer('parent_id') ?: null;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('regions', 'name')->where('parent_id', $parentId)->ignore($regionId),
            ],
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('regions', 'slug')->where('parent_id', $parentId)->ignore($regionId),
            ],
            'parent_id' => ['nullable', 'integer', Rule::exists('regions', 'id')],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $regionId = $this->route('region')?->id;
            $parentId = $this->integer('parent_id') ?: null;

            if (! $regionId || ! $parentId) {
                return;
            }

            if ($parentId === $regionId) {
                $validator->errors()->add('parent_id', "Hudud o`zining ota-hududi bo`la olmaydi.");

                return;
            }

            if (in_array($parentId, $this->descendantIds($regionId), true)) {
                $validator->errors()->add('parent_id', "Hudud o`z farzand hududiga ko`chirilishi mumkin emas.");
            }
        });
    }

    private function descendantIds(int $regionId): array
    {
        $ids = [];

        $collect = function (int $id) use (&$collect, &$ids) {
            foreach (Region::query()->where('parent_id', $id)->pluck('id') as $childId) {
                $ids[] = $childId;
                $collect($childId);
            }
        };

        $collect($regionId);
        return $ids;
    }
}
