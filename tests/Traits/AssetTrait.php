<?php

namespace Tests\Traits;

use App\Asset;
use App\Processors\Image\Breakpoints\Breakpoint;
use App\Processors\Image\Breakpoints\Large;
use App\Processors\Image\Breakpoints\Medium;
use App\Processors\Image\Breakpoints\Small;
use App\Processors\Image\Breakpoints\XLarge;
use Illuminate\Support\Collection;

trait AssetTrait
{
    /**
     * Get asset preload.
     *
     * @param  \App\Asset  $asset
     * @return array
     */
    protected function preload(Asset $asset): array
    {
        if (empty($asset->variants)) {
            return [];
        }

        $urls = array_merge([$asset->url], array_map(function(array $variant) {
            return $variant['url'];
        }, $asset->variants));

        return array_unique(array_values($urls));
    }

    /**
     * Get asset variants.
     *
     * @param  \App\Asset  $asset
     * @param  bool  $same
     * @return \Illuminate\Support\Collection
     */
    protected function variants(Asset $asset, bool $same = false): Collection
    {
        if (empty($asset->variants)) {
            return new Collection;
        }

        $method = $same ? 'sameVariant' : 'prefixedVariant';

        return collect([new Small, new Medium, new Large, new XLarge])
            ->flatMap(function (Breakpoint $breakpoint) use ($asset, $method) {
                return [
                    $breakpoint->index() => $this->{$method}($asset, $breakpoint)
                ];
            });
    }

    /**
     * Get variant with the path to the original file.
     *
     * @param  \App\Asset  $asset
     * @return array
     */
    private function sameVariant(Asset $asset): array
    {
        return [
            'path' => $asset->path,
            'url' => $asset->url,
        ];
    }

    /**
     * Get breakpoint prefixed variant.
     *
     * @param  \App\Asset  $asset
     * @param  \App\Processors\Image\Breakpoints\Breakpoint  $breakpoint
     * @return array
     */
    private function prefixedVariant(Asset $asset, Breakpoint $breakpoint): array
    {
        $path = pathinfo($asset->path);
        $url = pathinfo($asset->url);
        $file = $breakpoint->index().'-'.$path['basename'];

        return [
            'path' => $path['dirname'].'/'.$file,
            'url' => $url['dirname'].'/'.$file,
        ];
    }
}
