<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\RedirectResponse;

class BannerClickController extends Controller
{
    public function __invoke(Banner $banner): RedirectResponse
    {
        if ($banner->isActive()){
            $banner->recordClick();
        }
        return redirect()->away($banner->url);
    }
}
