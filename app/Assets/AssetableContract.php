<?php

namespace App\Assets;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface AssetableContract
 *
 * @package App\Assets
 *
 * @property \Illuminate\Support\Collection $assets
 */
interface AssetableContract
{
    /**
     * Get associated assets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function assets(): MorphMany;
}
