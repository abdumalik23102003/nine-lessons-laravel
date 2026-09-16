<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this ->url,
            'format' => $this->format,
            'file_url' => $this->getFileUrl(),
            'status' => $this->status,
            'views' => $this->views,
            'clicks' => $this->clicks,
            'rejection_reason' => $this->rejection_reason,
            'published_at' => $this->published_at?->toIso8601String(),
            'expired_at' => $this->expires_at?->toIso8601String(),
        ];
    }
}
