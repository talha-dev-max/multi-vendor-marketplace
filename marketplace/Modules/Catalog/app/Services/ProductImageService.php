<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Modules\Catalog\Exceptions\TooManyProductImagesException;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductImage;
use Modules\Catalog\Repositories\Contracts\ProductImageRepositoryInterface;

class ProductImageService
{
    public function __construct(
        private readonly ProductImageRepositoryInterface $images,
    ) {
    }

    public function upload(Product $product, UploadedFile $file, bool $isPrimary = false): ProductImage
    {
        $currentCount = $this->images->countForProduct($product);
        $max = (int) config('marketplace.products.max_images_per_product', 6);

        if ($currentCount >= $max) {
            throw new TooManyProductImagesException("Maximum of {$max} images per product.");
        }

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid()->toString();
        $dir = "products/{$product->id}";

        $originalPath = "{$dir}/{$filename}.{$extension}";
        Storage::disk('public')->putFileAs($dir, $file, "{$filename}.{$extension}");

        $thumbPath = null;
        try {
            $manager = new ImageManager(new GdDriver());
            $thumbWidth = (int) config('marketplace.products.image_thumb_width', 200);
            $thumbHeight = (int) config('marketplace.products.image_thumb_height', 200);

            $thumbImg = $manager->read(Storage::disk('public')->path($originalPath))
                ->scaleDown(width: $thumbWidth, height: $thumbHeight);

            $thumbPath = "{$dir}/{$filename}_thumb.{$extension}";
            Storage::disk('public')->put($thumbPath, (string) $thumbImg->encode());
        } catch (\Throwable $e) {
            // Fall through — original is saved even if thumb generation fails
            $thumbPath = null;
        }

        return $this->images->create(
            product: $product,
            path: $originalPath,
            thumbPath: $thumbPath,
            isPrimary: $isPrimary || $currentCount === 0,
            sortOrder: $currentCount,
        );
    }

    public function delete(Product $product, int $imageId): bool
    {
        $image = $this->images->findByIdForProduct($imageId, $product->id);

        if ($image === null) {
            return false;
        }

        if ($image->path !== null) {
            Storage::disk('public')->delete($image->path);
        }
        if ($image->thumb_path !== null) {
            Storage::disk('public')->delete($image->thumb_path);
        }

        return $this->images->delete($image);
    }
}
