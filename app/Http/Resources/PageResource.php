<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'menu_title' => $this->getMenuTitle(),
            'slug' => $this->slug,
            'content' => $this->content,
        ];
    }
}
