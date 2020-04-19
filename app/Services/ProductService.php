<?php

namespace App\Services;

use App\Asset;
use App\Assets\Type\Image;
use App\Assets\UploadedAsset;
use App\Assets\AssetableContract;
use App\Repositories\Contracts\AssetRepositoryContract;

class ProductService
{
    /**
     * @var \App\Repositories\Contracts\AssetRepositoryContract
     */
    private AssetRepositoryContract $assetRepository;

    /**
     * ProductService constructor.
     *
     * @param  \App\Repositories\Contracts\AssetRepositoryContract  $assetRepository
     */
    public function __construct(AssetRepositoryContract $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    /**
     * Save product image.
     *
     * @param  \App\Assets\AssetableContract  $product
     * @param  \App\Assets\UploadedAsset  $file
     * @return \App\Asset
     */
    public function saveImage(AssetableContract $product, UploadedAsset $file): Asset
    {
        return $this->assetRepository->create($product, Image::class, $file); // todo add processors
    }
}
