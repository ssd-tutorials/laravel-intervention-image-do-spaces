<?php

namespace App;

use App\Traits\Sortable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Asset
 *
 * @package App
 *
 * @property int $id
 * @property int $assetable_id
 * @property string $assetable_type
 * @property string $type
 * @property string $disk
 * @property string $visibility
 * @property string $path
 * @property string $original_name
 * @property string $extension
 * @property string $mime
 * @property int $size
 * @property string|null $caption
 * @property array $variants
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property \App\Assets\AssetableContract $assetable
 * @property string $url
 */
class Asset extends Model
{
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assetable_id',
        'assetable_type',
        'type',
        'disk',
        'visibility',
        'sort',
        'path',
        'original_name',
        'extension',
        'mime',
        'size',
        'caption',
        'variants',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'variants' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
    ];

    /**
     * Get assetable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function assetable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get url attribute.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
