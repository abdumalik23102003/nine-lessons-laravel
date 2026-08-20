<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Regions\RegionRequest;
use App\Models\Region;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(): View
    {
        return view('admin.regions.index', [
            'regions' => Region::query()->with('parent')->orderBy('name')->paginate(30),
        ]);
    }

    public function create(): View
    {
        return view('admin.regions.create', [
            'region' => new Region(),
            'tree' => Region::tree(),
        ]);
    }

    public function store(RegionRequest $request)
    {
        Region::query()->create($request->validated());

        return redirect()->route('admin.regions.index')->with('status', "Hudud qo`shildi");
    }

    public function edit(Region $region): View
    {
        return view('admin.regions.edit', [
            'region' => $region,
            'tree' => Region::tree(),
        ]);
    }

    public function update(RegionRequest $request, Region $region): RedirectResponse
    {
        $region->update($request->validated());

        return redirect()->route('admin.regions.index')->with('status', "Hudud yangilandi.");
    }

    public function destroy(Region $region): RedirectResponse
    {
        try {
            $region->delete();
        } catch (QueryException) {
            return back()->with('error', "Bu hududda e`lonlar mavjud, avval ularni boshqa hududga ko`chiring.");
        }

        return redirect()->route('admin.regions.index')->with('status', "Hudud o'chirildi!");
    }
}
