<?php

namespace App\Http\Requests\Cabinet\Adverts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Advert $advert */
        $advert = $this->route('advert');
        $rules = [];

        foreach ($advert->category->allAttributes() as $attribute) {
            $fieldRules = [$attribute->required ? 'required' : 'nullable'];

            if ($attribute->isInteger()) {
                $fieldRules[] = 'integer';
            } elseif ($attribute->isFloat()) {
                $fieldRules[] = 'numeric';
            } else {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:255';
            }

            if ($attribute->isSelect()){
                $fieldRules[] = Rule::in($attribute->variants);
            }

            $rules['attributes.' . $attribute->id] = $fieldRules;
        }
        return $rules;
    }
}
