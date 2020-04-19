<?php

namespace App\Services;

use App\Assets\UploadedAsset;
use App\Assets\AssetUploadRequestContract;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload file and save as Asset.
     *
     * @param  \App\Assets\AssetUploadRequestContract  $request
     * @param  \Closure|null  $processor
     * @return \App\Assets\UploadedAsset
     */
    public function upload(AssetUploadRequestContract $request, Closure $processor = null): UploadedAsset
    {
        $file = $this->parseFile($request->file($request->field()), $processor);

        $path = Storage::putFile(
            $request->directory(),
            $file,
            [
                'disk' => $disk = $request->disk(),
                'visibility' => $visibility = $request->visibility(),
            ]
        );

        return new UploadedAsset($file, $path, $disk, $visibility);
    }

    /**
     * Get uploaded file
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  \Closure|null  $processor
     * @return \Illuminate\Http\UploadedFile
     */
    private function parseFile(UploadedFile $file, ?Closure $processor): UploadedFile
    {
        if (is_null($processor)) {
            return $file;
        }

        return new UploadedFile(
            (call_user_func($processor, $file)),
            $file->getClientOriginalName(),
            $file->getClientMimeType()
        );
    }
}
