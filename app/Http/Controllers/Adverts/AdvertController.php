<?php

namespace App\Http\Controllers\Adverts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\SearchRequest;
use App\Models\Advert;
use App\Models\Category;
use App\Models\Region;
use App\Services\Search\AdvertSearchService;
use Illuminate\View\View;

class AdvertController extends Controller
{
    public function index(SearchRequest $request, AdvertSearchService $search): View
    {
        return view('adverts.index', [
            'adverts' => $search->search($request),
            'categories' => Category::tree(),
            'regions' => Region::tree(),
        ]);
    }

    public function show(Advert $advert): View
    {
        $this->authorize('view', $advert);

        return view('adverts.show', [
            'advert' => $advert,
        ]);
    }
}
