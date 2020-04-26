<?php

namespace App\Http\Controllers;

use App\Product;
use App\Services\ProductService;
use App\Services\FileUploadService;
use App\Http\Resources\AssetResource;
use App\Http\Requests\Product\RemoveImageRequest;
use App\Http\Requests\Product\UploadImageRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;

class ProductImageController extends Controller
{
    /**
     * @var \Intervention\Image\ImageManager
     */
    private ImageManager $manager;

    /**
     * @var \App\Services\ProductService
     */
    private ProductService $productService;

    /**
     * @var \App\Services\FileUploadService
     */
    private FileUploadService $fileUploadService;

    /**
     * ProductImageController constructor.
     *
     * @param  \Intervention\Image\ImageManager  $manager
     * @param  \App\Services\ProductService  $productService
     * @param  \App\Services\FileUploadService  $fileUploadService
     */
    public function __construct(ImageManager $manager, ProductService $productService, FileUploadService $fileUploadService)
    {
        $this->manager = $manager;
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Store new image.
     *
     * @param  \App\Http\Requests\Product\UploadImageRequest  $request
     * @param  \App\Product  $product
     * @return string
     */
    public function store(UploadImageRequest $request, Product $product)
    {
        $asset = $this->productService->saveImage(
            $product, $this->fileUploadService->upload($request, function (UploadedFile $file) {
                if ($file->getClientMimeType() === 'image/svg+xml') {
                    return $file;
                }
                return $this->manager->make($file)->greyscale()->save()->basePath();
            })
        );

        return new AssetResource($asset);
    }

    /**
     * Remove image.
     *
     * @param  \App\Http\Requests\Product\RemoveImageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(RemoveImageRequest $request): JsonResponse
    {
        $this->productService->removeImage($request->file);

        return new JsonResponse(['success' => true]);
    }
}
