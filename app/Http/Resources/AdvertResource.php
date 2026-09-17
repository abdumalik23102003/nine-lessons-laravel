<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'price' => $this->price,
            'status' => $this->status,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'region' => $this->region ? [
                'id' => $this->region->id,
                'name' => $this->region->name,
            ] : null,
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'attributes' => $this->attributeValues(),
            'published_at' => $this->published_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
        ];
    }

    private function attributeValues(): array
    {
        return $this->values->map(function ($value) {
            return [
                'attribute_id' => $value->attribute_id,
                'attribute_name' => $value->attribute?->name,
                'value' => $value->value,
            ];
        })->all();
    }
}
