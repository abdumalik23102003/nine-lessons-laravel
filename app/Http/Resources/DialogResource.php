<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DialogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->user()?->id;

        return [
            'id' => $this->id,
            'advert' => [
                'id' => $this->advert->id,
                'title' => $this->advert->title,
            ],
            'companion' => $this->isOwner($userId)
                ? ['id' => $this->client->id, 'name' => $this->client->name]
                : ['id' => $this->user->id, 'name' => $this->user->name],
            'unread_count' => $this->unreadCountFor($userId),
            'messages' => DialogMessageResource::collection($this->whenLoaded('messages')),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
