<?php

namespace Tests\Feature;

use App\Asset;
use App\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Inline\Element\Strong;
use Tests\TestCase;
use Tests\Traits\AssetTrait;
use Tests\Traits\ResponseTrait;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductImageDestroyTest extends TestCase
{
    use RefreshDatabase, ResponseTrait, AssetTrait;

    /**
     * @var \App\Product
     */
    protected Product $product;

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'filesystems' => [
                'default' => 'public',
                'max_size' => 1000,
            ],
        ]);

        $this->product = factory(Product::class)->create();
    }

    /**
     * @test
     */
    public function validation_fails_with_missing_file_field()
    {
        $this->assertCount(0, $this->product->assets);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id));

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])],
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_with_empty_file_field()
    {
        $this->assertCount(0, $this->product->assets);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id), [
            'file' => '',
        ]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.required', ['attribute' => 'file'])],
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_with_invalid_file_field()
    {
        $this->assertCount(0, $this->product->assets);

        $response = $this->deleteJson(route('product.destroy_image', $this->product->id), [
            'file' => 1,
        ]);

        $this->assertResponseUnprocessableWithJson($response, [
            'file' => [__('validation.exists', ['attribute' => 'file'])],
        ]);
    }

    /**
     * @test
     */
    public function removes_image()
    {
        Storage::fake();

        $this->assertCount(0, $this->product->assets);


        $response = $this->postJson(route('product.store_image', $this->product->id), [
            'file' => UploadedFile::fake()->image('image.jpg'),
        ]);

        $this->product = $this->product->fresh('assets');

        $this->assertCount(1, $this->product->assets);

        $asset = $this->product->assets->first();

        $this->assertResponseCreated($response);

        Storage::assertExists($asset->path);

        collect($asset->variants)->each(function (array $variant) {
            Storage::assertExists($variant['path']);
        });

        $this->assertEquals(Storage::url($asset->path), $asset->url);


        $response = $this->deleteJson(route('product.destroy_image', $this->product->id), [
            'file' => $asset->id,
        ]);

        $this->assertResponseOkWithJson($response, ['success' => true]);

        $this->product = $this->product->fresh('assets');

        $this->assertCount(0, $this->product->assets);
        $this->assertCount(0, Asset::all());

        Storage::assertMissing($asset->path);

        collect($asset->variants)->each(function (array $variant) {
            Storage::assertMissing($variant['path']);
        });
    }
}
