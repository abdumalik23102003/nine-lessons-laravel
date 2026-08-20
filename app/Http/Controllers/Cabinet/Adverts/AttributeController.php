<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Adverts\AttributeRequest;
use App\Models\Advert;
use App\Models\Attribute;
use Illuminate\Http\RedirectResponse;

class AttributeController extends Controller
{
    public function update(AttributeRequest $request, Advert $advert): RedirectResponse
    {
        $this->authorize('update', $advert);
        foreach ($request->validated('attributes', []) as $arrtibuteId => $value) {
            $advert->values()->updateOrCreate(
                ['attribute_id' => $arrtibuteId],
                ['value' => $value],
            );
        }

        return redirect()
            ->route('cabinet.adverts.edit', $advert)
            ->with('status', "Xususiyatlar saqlandi");
    }
}
