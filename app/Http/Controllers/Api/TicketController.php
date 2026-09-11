<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketMessageRequest;
use App\Http\Requests\Tickets\TicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tickets = Ticket::forUser($request->user())->latest()->paginate(20);

        return TicketResource::collection($tickets);
    }

    public function store(TicketRequest $request): JsonResponse
    {
        $ticket = Ticket::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => Ticket::STATUS_OPEN,
        ]);

        return (new TicketResource($ticket))->response()->setStatusCode(201);
    }

    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);

        return new TicketResource($ticket->load('messages.user'));
    }

    public function addMessage(TicketMessageRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('view', $ticket);

        $user = $request->user();

        try {
            $ticket->addMessage($user->id, $request->validated('message'), fromStaff: $user->canModerate());
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new TicketResource($ticket->load('messages.user')))->response()->setStatusCode(201);
    }

    public function close(Ticket $ticket): TicketResource|JsonResponse
    {
        $this->authorize('view', $ticket);

        try {
            $ticket->close();
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new TicketResource($ticket);
    }
}
