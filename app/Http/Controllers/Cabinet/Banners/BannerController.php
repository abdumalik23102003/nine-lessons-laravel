<?php

namespace App\Http\Controllers\Cabinet\Banners;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Banners\BannerRequest;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        return view('cabinet.banners.index', [
            'banners' => Banner::query()->forUser($request->user())->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('cabinet.banners.create', [
            'banner' => new Banner(),
            'categories' => Category::query()->orderBy('name')->get(),
            'regions' => Region::query()->orderBy('name')->get(),
        ]);
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = Banner::STATUS_DRAFT;

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('banners/' . now()->format('y/m/d'), 'public');
        }

        $banner = Banner::query()->create($data);

        return redirect()->route('cabinet.banners.edit', $banner)->with('status', "Banner yaratildi.");
    }

    public function edit(Banner $banner): View
    {
        $this->authorize('update', $banner);

        return view('cabinet.banners.edit', [
            'banner' => $banner,
            'categories' => Category::query()->orderBy('name')->get(),
            'regions' => Region::query()->orderBy('name')->get(),
        ]);
    }

    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->authorize('update', $banner);

        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($banner->file) {
                Storage::disk('public')->delete($banner->file);
            }
            $data['file'] = $request->file('file')->store('banners/' . now()->format('y/m/d'), 'public');
        }

        $banner->update($data);

        return redirect()->route('cabinet.banners.edit', $banner)->with('status', "Banner yangilandi.");
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->authorize('update', $banner);

        if ($banner->file) {
            Storage::disk('public')->delete($banner->file);
        }
        $banner->delete();

        return redirect()->route('cabinet.banners.index')->with('status', "Banner o'chirildi.");
    }

    public function sendToModeration(Banner $banner): RedirectResponse
    {
        $this->authorize('update', $banner);

        try {
            $banner->sendToModeration();
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.banners.edit', $banner)->with('status', "Moderatsiyaga yuborildi.");
    }
}
