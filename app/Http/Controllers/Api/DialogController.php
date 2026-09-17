<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dialogs\DialogMessageRequest;
use App\Http\Resources\DialogResource;
use App\Models\Advert;
use App\Models\Dialog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class DialogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $dialogs = Dialog::forUser($request->user()->id)
            ->with(['advert', 'user', 'client'])
            ->latest('updated_at')
            ->paginate(20);

        return DialogResource::collection($dialogs);
    }

    public function show(Request $request, Dialog $dialog): DialogResource
    {
        try {
            $this->authorize('view', $dialog);
        } catch (AuthorizationException $e) {
            Log::error($e->getMessage());
        }

        $dialog->readBy($request->user()->id);

        return new DialogResource($dialog->load('messages.user', 'advert', 'user', 'client'));
    }

    public function start(Request $request, Advert $advert): JsonResponse
    {
        abort_if($advert->user_id === $request->user()->id, 403, "O'z e'loningizga xabar yoza olmaysiz.");

        $dialog = Dialog::firstOrCreate([
            'advert_id' => $advert->id,
            'user_id' => $advert->user_id,
            'client_id' => $request->user()->id,
        ]);

        return (new DialogResource($dialog->load('advert', 'user', 'client')))
            ->response()
            ->setStatusCode(201);
    }

    public function addMessage(DialogMessageRequest $request, Dialog $dialog): JsonResponse
    {
        try {
            $this->authorize('view', $dialog);
        } catch (AuthorizationException $e) {
            Log::error($e->getMessage());
        }

        $dialog->addMessage($request->user()->id, $request->validated('message'));

        return (new DialogResource($dialog->load('messages.user', 'advert', 'user', 'client')))
            ->response()
            ->setStatusCode(201);
    }
}
