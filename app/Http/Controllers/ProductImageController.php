<?php

namespace App\Http\Controllers;

use App\Product;
use App\Services\ProductService;
use App\Services\FileUploadService;
use App\Http\Resources\AssetResource;
use App\Http\Requests\Product\UploadImageRequest;

use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
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
     * @param  \App\Services\ProductService  $productService
     * @param  \App\Services\FileUploadService  $fileUploadService
     */
    public function __construct(ProductService $productService, FileUploadService $fileUploadService)
    {
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
            $product, $this->fileUploadService->upload($request) // todo add closure processor
        );

        return new AssetResource($asset);
    }

    /**
     * Remove image.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        return new JsonResponse(['success' => true]);
    }
}
