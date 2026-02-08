<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Get paginated products with first photo (optimized for list).
     * For admin: lists all products with optional filters.
     * For vendor: pass vendor to scope to their products.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(?Vendor $vendor = null, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $vendor
            ? $vendor->products()
            : Product::query();

        $query->with(['photos' => function ($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
        }]);

        // For admin: filter by vendor_id (only if not scoped to a vendor)
        if (! $vendor && isset($filters['vendor_id']) && $filters['vendor_id']) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        // Filter by status (product approval status)
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Filter by active status
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // For admin: eager load vendor relationship
        if (! $vendor) {
            $query->with('vendor.user');
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Create a product.
     * If vendor is provided, it's scoped to that vendor.
     * Otherwise, vendor_id must be in $data (admin use case).
     *
     * @param  array<string, mixed>  $data
     */
    public function create(?Vendor $vendor, array $data): Product
    {
        // Set default status to pending if not provided
        if (! isset($data['status'])) {
            $data['status'] = Product::STATUS_PENDING;
        }

        if ($vendor) {
            $product = $vendor->products()->create($data);
        } else {
            $product = Product::query()->create($data);
        }

        return $product->load($vendor ? 'photos' : ['vendor.user', 'photos']);
    }

    /**
     * Update an existing product.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh($product->vendor ? ['vendor.user', 'photos'] : 'photos');
    }

    /**
     * Delete a product and its photos from storage.
     */
    public function delete(Product $product): void
    {
        // Delete all photos from storage
        foreach ($product->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $product->delete();
    }

    /**
     * Toggle product active status.
     */
    public function toggleActive(Product $product): Product
    {
        $product->update(['is_active' => ! $product->is_active]);

        return $product->fresh(['vendor.user', 'photos']);
    }

    /**
     * Update product status (admin only).
     */
    public function updateStatus(Product $product, string $status): Product
    {
        $product->update(['status' => $status]);

        return $product->fresh(['vendor.user', 'photos']);
    }

    /**
     * Set primary photo for a product.
     */
    public function setPrimaryPhoto(Product $product, ProductPhoto $photo): Product
    {
        // Ensure photo belongs to product
        if ($photo->product_id !== $product->id) {
            throw new \InvalidArgumentException('Photo does not belong to this product.');
        }

        // Unset all other primary photos for this product
        $product->photos()->where('is_primary', true)->update(['is_primary' => false]);

        // Set this photo as primary
        $photo->update(['is_primary' => true]);

        return $product->fresh(['vendor.user', 'photos']);
    }

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

        // Check if there's already a primary photo
        $hasPrimary = $product->photos()->where('is_primary', true)->exists();

        foreach ($files as $index => $file) {
            $path = $file->store('products/'.$product->id, 'public');

            $photos[] = $product->photos()->create([
                'path' => $path,
                'sort_order' => ++$maxOrder,
                'is_primary' => ! $hasPrimary && $index === 0, // Set first photo as primary if none exists
            ]);

            if (! $hasPrimary && $index === 0) {
                $hasPrimary = true;
            }
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
        // Get photos to delete
        $photos = $product->photos()->whereIn('id', $photoIds)->get();

        // Check if any of the photos being deleted is primary
        $deletedPrimary = $photos->where('is_primary', true)->isNotEmpty();

        // Delete photos and their files
        foreach ($photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        // If the primary photo was among the deleted ones, set the first remaining photo as primary
        if ($deletedPrimary) {
            // Refresh product to get updated photos list
            $product->refresh();
            $firstRemaining = $product->photos()->orderBy('sort_order')->first();
            if ($firstRemaining) {
                $firstRemaining->update(['is_primary' => true]);
            }
        }

        return $photos->count();
    }
}
