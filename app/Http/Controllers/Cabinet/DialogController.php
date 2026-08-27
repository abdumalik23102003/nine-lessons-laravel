<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dialogs\DialogMessageRequest;
use App\Models\Advert;
use App\Models\Dialog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DialogController extends Controller
{
    public function index(Request $request): View
    {
        return view('cabinet.dialogs.index', [
            'dialogs' => Dialog::query()
                ->forUser($request->user()->id)
                ->with(['advert', 'user', 'client'])
                ->latest('updated_at')
                ->paginate(20),
        ]);
    }

    public function show(Request $request, Dialog $dialog): View
    {
        $this->authorize('view', $dialog);

        $dialog->readBy($request->user()->id);

        return view('cabinet.dialogs.show', [
            'dialog' => $dialog->load('messages.user', 'advert', 'user', 'client'),
        ]);
    }

    public function start(Request $request, Advert $advert): RedirectResponse
    {
        abort_if($advert->user_id === $request->user()->id, 403, "O'z e'loningizga xabar yoza olmaysiz.");

        $dialog = Dialog::query()->firstOrCreate([
            'advert_id' => $advert->id,
            'user_id' => $advert->user_id,
            'client_id' => $request->user()->id,
        ]);

        return redirect()->route('cabinet.dialogs.show', $dialog);
    }

    public function addMessage(DialogMessageRequest $request, Dialog $dialog): RedirectResponse
    {
        $this->authorize('view', $dialog);

        $dialog->addMessage($request->user()->id, $request->validated('message'));

        return redirect()->route('cabinet.dialogs.show', $dialog);
    }
}
