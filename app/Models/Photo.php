<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use  HasFactory;
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $table = 'advert_photos';

    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class);
    }

    public function getFileUrl(): string
    {
        return asset('storage/' . $this->file);
    }

    public static function deleteEmptyDirectories(string $path): void
    {
        $disk = Storage::disk('public');

        $dir = dirname($path);

        while ($dir !== 'adverts' && $dir !== '.') {
            if ($disk->files($dir) || $disk->directories($dir)) {
                break;
            }

            $disk->deleteDirectory($dir);
            $dir = dirname($dir);
        }
    }
}
