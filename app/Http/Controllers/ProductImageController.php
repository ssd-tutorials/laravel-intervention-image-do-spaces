<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\UploadImageRequest;

use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    /**
     * Store new image.
     *
     * @param  \App\Http\Requests\Product\UploadImageRequest  $request
     * @return string
     */
    public function store(UploadImageRequest $request)
    {
        return '';
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
