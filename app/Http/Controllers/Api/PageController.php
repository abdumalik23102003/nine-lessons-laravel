<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PageResource::collection(
            Page::query()->inMenu()->orderBy('title')->get()
        );
    }

    public function show(Page $page): PageResource
    {
        return new PageResource($page);
    }
}
