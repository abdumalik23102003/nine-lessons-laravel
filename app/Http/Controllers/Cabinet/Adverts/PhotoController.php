<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Adverts\PhotoRequest;
use App\Models\Advert;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(PhotoRequest $request, Advert $advert): RedirectResponse
    {
        $this->authorize('update', $advert);
        $path = $request->file('file')->store('adverts/' . \Illuminate\Support\now()->format('y/m/d'), 'public');
        $advert->photos()->create(['file' => $path]);
        return redirect()
            ->route('cabinet.adverts.edit', ['advert' => $advert])
            ->with('status', "Rasm yuklandi.");
    }

    public function destroy(Advert $advert, Photo $photo): RedirectResponse
    {
        $this->authorize('update', $advert);
        Storage::disk('public')->delete($photo->file);

        Photo::deleteEmptyDirectories($photo->file);

        $photo->delete();

        return redirect()
            ->route('cabinet.adverts.edit', $advert)
            ->with('status', "Rasm o`chirildi.");
    }


}
