<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductPhotoService
{
    /**
     * Store multiple photos for a product.
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, ProductPhoto>
     */
    public function addPhotos(Product $product, array $files): array
    {
        $maxOrder = $product->photos()->max('sort_order') ?? 0;
        $photos = [];

        foreach ($files as $file) {
            $path = $file->store('products/'.$product->id, 'public');

            $photos[] = $product->photos()->create([
                'path' => $path,
                'sort_order' => ++$maxOrder,
            ]);
        }

        return $photos;
    }

    /**
     * Remove a single photo by ID.
     */
    public function removePhoto(ProductPhoto $photo): void
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
    }

    /**
     * Remove multiple photos by IDs (scoped to a product).
     *
     * @param  array<int, int>  $photoIds
     */
    public function removePhotos(Product $product, array $photoIds): int
    {
        $photos = $product->photos()->whereIn('id', $photoIds)->get();

        foreach ($photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        return $photos->count();
    }
}
