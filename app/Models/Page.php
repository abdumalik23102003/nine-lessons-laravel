<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'show_in_menu' => 'boolean',
    ];

    public function getMenuTitle(): string
    {
        return $this->menu_title ?: $this->title;
    }

    public function scopeInMenu(Builder $query): Builder
    {
        return $query->where('show_in_menu', true);
    }
}
