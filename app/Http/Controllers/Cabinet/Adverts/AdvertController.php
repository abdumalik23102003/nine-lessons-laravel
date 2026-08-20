<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Adverts\AdvertRequest;
use App\Models\Advert;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertController extends Controller
{
    public function index(Request $request)
    {
        return view('cabinet.adverts.index', [
            'adverts' => Advert::forUser($request->user())
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return view('cabinet.adverts.create', [
            'categories' => Category::tree(),
            'regions' => Region::tree(),
        ]);
    }

    public function store(AdvertRequest $request): RedirectResponse
    {
        $advert = Advert::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('cabinet.adverts.edit', $advert)
            ->with('status', "E'lon qoralama sifatida saqlandi.");
    }

    public function edit(Advert $advert): View
    {
        $this->authorize('update', $advert);

        return view('cabinet.adverts.edit', [
            'advert' => $advert,
            'categories' => Category::tree(),
            'regions' => Region::tree(),
        ]);
    }

    public function update(AdvertRequest $request, Advert $advert): RedirectResponse
    {
        $this->authorize('update', $advert);
        $advert->update($request->validated());

        return redirect()
            ->route('cabinet.adverts.edit', $advert)
            ->with('status', "Saqlandi.");
    }

    public function destroy(Advert $advert): RedirectResponse
    {
        $this->authorize('update', $advert);
        $advert->delete();
        return redirect()
            ->route('cabinet.adverts.index')
            ->with('status', "E'lon o`chirildi.");
    }

    public function sendToModeration(Advert $advert): RedirectResponse
    {
        $this->authorize('update', $advert);

        try {
            $advert->sendToModeration();
        } catch (\DomainException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()
            ->route('cabinet.adverts.edit', $advert)
            ->with('status', "Moderatsiyaga yuborildi.");
    }
}
