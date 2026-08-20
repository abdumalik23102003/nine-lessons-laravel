<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Region;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'regions' => Region::roots()->orderBy('slug')->get(),
            'categories' => Category::roots()->orderBy('slug')->get(),
        ]);
    }
}
