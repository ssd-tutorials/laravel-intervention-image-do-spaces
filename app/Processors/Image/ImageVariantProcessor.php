<?php

namespace App\Processors\Image;

use App\Assets\Variant;
use App\Assets\UploadedAsset;
use App\Processors\Image\Breakpoints\Small;
use App\Processors\Image\Breakpoints\Large;
use App\Processors\Image\Breakpoints\Medium;
use App\Processors\Image\Breakpoints\XLarge;
use App\Processors\Image\Breakpoints\Breakpoint;

use Closure;
use InvalidArgumentException;
use Intervention\Image\ImageManager;
use Psr\Http\Message\StreamInterface;
use Illuminate\Support\Facades\Storage;

class ImageVariantProcessor
{
    /**
     * @var \Intervention\Image\ImageManager
     */
    private ImageManager $manager;

    /**
     * ImageVariantProcessor constructor.
     *
     * @param  \Intervention\Image\ImageManager  $manager
     */
    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Generate variants.
     *
     * @param  \App\Assets\UploadedAsset  $file
     * @param  array  $variants
     * @param  \Closure|null  $process
     * @return array
     */
    public function generateVariants(UploadedAsset $file, array $variants = [], Closure $process = null): array
    {
        if (empty($variants) || $file->mime === 'image/svg+xml') {
            return $this->generateSoleVariants($file);
        }

        return $this->flatVariants($variants, function (Breakpoint $breakpoint) use ($file, $process) {
            return $this->process($breakpoint, $file, $process);
        });
    }

    /**
     * Generate sole variants using main file for all.
     *
     * @param  \App\Assets\UploadedAsset  $file
     * @return array
     */
    private function generateSoleVariants(UploadedAsset $file): array
    {
        $variant = new Variant($file->path, $file->disk);

        return $this->flatVariants(
            [new Small, new Medium, new Large, new XLarge],
            function () use ($variant) {
                return $variant;
            }
        );
    }

    /**
     * Parse variants.
     *
     * @param  array  $variants
     * @param  \Closure  $process
     * @return array
     */
    private function flatVariants(array $variants, Closure $process): array
    {
        return collect($variants)->flatMap(function (Breakpoint $breakpoint) use ($process) {
            return [$breakpoint->index() => call_user_func($process, $breakpoint)];
        })->toArray();
    }

    /**
     * Run process.
     *
     * @param  \App\Processors\Image\Breakpoints\Breakpoint  $breakpoint
     * @param  \App\Assets\UploadedAsset  $file
     * @param  \Closure|null  $process
     * @return \App\Assets\Variant
     */
    private function process(Breakpoint $breakpoint, UploadedAsset $file, ?Closure $process): Variant
    {
        $callback = !is_null($breakpoint->process) ? $breakpoint->process : $process;

        if (is_null($callback)) {
            throw new InvalidArgumentException('Process is missing');
        }

        $source = call_user_func(
            $callback, $this->manager->make($file->file->path()), $breakpoint
        );

        $path = $this->store($file, $breakpoint, $source->stream());

        return new Variant($path, $file->disk);
    }

    /**
     * Store processed image.
     *
     * @param  \App\Assets\UploadedAsset  $file
     * @param  \App\Processors\Image\Breakpoints\Breakpoint  $breakpoint
     * @param  \Psr\Http\Message\StreamInterface  $stream
     * @return string
     */
    private function store(UploadedAsset $file, Breakpoint $breakpoint, StreamInterface $stream): string
    {
        Storage::put(
            $filePath = $file->directory.ds().$breakpoint->index().'-'.$file->fileName,
            $stream,
            [
                'disk' => $file->disk,
                'visibility' => $file->visibility,
            ]
        );

        return $filePath;
    }
}
