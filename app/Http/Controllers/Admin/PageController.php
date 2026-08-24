<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pages\PageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->orderBy('title')->paginate(30),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', [
            'page' => new Page(),
        ]);
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['show_in_menu'] = $request->boolean('show_in_menu');

        Page::query()->create($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page yaratildi.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $data['show_in_menu'] = $request->boolean('show_in_menu');

        $page->update($data);
        return redirect()->route('admin.pages.index')->with('status', 'Page yangilandi.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page o`chirildi.');
    }
}
