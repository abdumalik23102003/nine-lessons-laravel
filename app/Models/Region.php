<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Region extends Model
{

    use HasFactory;
    protected $fillable = ['name', 'slug', 'parent_id'];
    public function getPath(): string
    {
        return ($this->parent ? $this->parent->getPath() . '/' : '') . $this->slug;
    }

    public function getAddress(): string
    {
        return ($this->parent ? $this->parent->getAddress() . ', ' : '') . $this->name;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function tree(): array
    {
        $result = [];
        $build = function (?int $parentId, int $depth) use (&$result, &$build) {
            foreach (self::where('parent_id', $parentId)->orderBy('name')->get() as $region) {
                $result[] = ['id' => $region->id, 'name' => str_repeat('— ', $depth) . $region->name];
                $build($region->id, $depth + 1);
            }
        };
        $build(null, 0);
        return $result;
    }
}
