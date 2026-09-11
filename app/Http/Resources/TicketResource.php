<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'content' => $this->content,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'messages' => TicketMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
