<?php

namespace App\Repositories\Contracts;

use App\Asset;
use App\Assets\UploadedAsset;
use App\Assets\AssetableContract;

use Closure;

interface AssetRepositoryContract
{
    /**
     * Create new record for a model.
     *
     * @param  \App\Assets\AssetableContract  $model
     * @param  string  $type
     * @param  \App\Assets\UploadedAsset  $file
     * @param  array  $variants
     * @param  \Closure|null  $process
     * @return mixed
     */
    public function create(
        AssetableContract $model,
        string $type,
        UploadedAsset $file,
        array $variants = [],
        Closure $process = null
    ): Asset;

    /**
     * Get next sort for a type.
     *
     * @param  \App\Assets\AssetableContract  $model
     * @param  string  $type
     * @return mixed
     */
    public function nextSortFor(AssetableContract $model, string $type): int;

    /**
     * Remove record.
     *
     * @param  \Illuminate\Support\Collection|\App\Asset  $assets
     * @return void
     */
    public function remove($assets): void;
}
