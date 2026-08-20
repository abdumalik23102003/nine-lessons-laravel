<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{

    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name', 'slug', 'parent_id'];

    public function getPath(): string
    {
        return ($this->parent ? $this->parent->getPath() . '/' : '') . $this->slug;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    public function allAttributes(): array
    {
        $parentAttrs = $this->parent ? $this->parent->allAttributes() : [];
        return [...$parentAttrs, ...$this->attributes()->orderBy('sort')->get()->all()];
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function ancestorIds(): array
    {
        return $this->parent ? [...$this->parent->ancestorIds(), $this->parent->id] : [];
    }

    public static function tree()
    {
        $result = [];
        $build = function (?int $parentId, int $depth) use (&$result, &$build) {
            foreach (self::where('parent_id', $parentId)->orderBy('name')->get() as $category) {
                $result[] = ['id' => $category->id, 'name' => str_repeat('- ', $depth) . $category->name];
                $build($category->id, $depth + 1);
            }
        };
        $build(null, 0);
        return $result;
    }
}
