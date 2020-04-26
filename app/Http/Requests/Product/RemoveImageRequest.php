<?php

namespace App\Http\Requests\Product;

use App\Product;
use App\Assets\Type\Image;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RemoveImageRequest
 *
 * @package App\Http\Requests\Product
 *
 * @property int $file
 */
class RemoveImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                Rule::exists('assets', 'id')->where(function (Builder $query) {
                    $query->where([
                        'type' => Image::class,
                        'assetable_id' => $this->route('product')->id,
                        'assetable_type' => Product::class,
                    ]);
                }),
            ],
        ];
    }
}
