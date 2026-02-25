<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
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
        }, 'subcategory.category']);

        // For admin: filter by vendor_id (only if not scoped to a vendor)
        if (! $vendor && isset($filters['vendor_id']) && $filters['vendor_id']) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        // Filter by category_id via product subcategory relation
        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->whereHas('subcategory', function ($subcategoryQuery) use ($filters): void {
                $subcategoryQuery->where('category_id', $filters['category_id']);
            });
        }

        // Filter by subcategory_id
        if (isset($filters['subcategory_id']) && $filters['subcategory_id']) {
            $query->where('subcategory_id', $filters['subcategory_id']);
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
     * Get paginated public products (for clients/users).
     * Only shows products that:
     * - Vendor is active
     * - Product is active
     * - Quantity > 0
     * - Status is approved
     *
     * @param  array<string, mixed>  $filters
     */
    public function listPublic(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $vendorId = isset($filters['vendor_id']) && $filters['vendor_id'] !== '' && $filters['vendor_id'] !== null
            ? (int) $filters['vendor_id']
            : null;

        $categoryId = isset($filters['category_id']) && $filters['category_id'] !== '' && $filters['category_id'] !== null
            ? (int) $filters['category_id']
            : null;

        $page = request()->get('page', 1);
        $cacheKey = "products:public:v:{$vendorId}:c:{$categoryId}:page:{$page}";

        try {
            return Cache::remember($cacheKey, 1800, function () use ($perPage, $vendorId, $categoryId) {
                return $this->fetchPublicProducts($perPage, $vendorId, $categoryId);
            });
        } catch (\Exception $e) {
            return $this->fetchPublicProducts($perPage, $vendorId, $categoryId);
        }
    }

    protected function fetchPublicProducts(int $perPage, ?int $vendorId, ?int $categoryId = null): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('is_active', true)
            ->where('status', Product::STATUS_APPROVED)
            ->where('quantity', '>', 0)
            ->whereHas('vendor', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['photos' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
            }, 'vendor:id,store_name,user_id', 'vendor.user:id,name']);

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        if ($categoryId) {
            $query->whereHas('subcategory', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        return $query->latest('created_at')->paginate($perPage);
    }

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

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $product->load($vendor ? ['photos', 'subcategory.category'] : ['vendor.user', 'photos', 'subcategory.category']);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $product->fresh($product->vendor ? ['vendor.user', 'photos', 'subcategory.category'] : ['photos', 'subcategory.category']);
    }

    /**
     * Delete a product and its photos from storage.
     */
    public function delete(Product $product): void
    {
        $vendorId = $product->vendor_id;

        // Delete all photos from storage
        foreach ($product->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $product->delete();

        // Invalidate cache
        $this->invalidateProductCache($product, $vendorId);
    }

    /**
     * Toggle product active status.
     */
    public function toggleActive(Product $product): Product
    {
        $product->update(['is_active' => ! $product->is_active]);

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $product->fresh(['vendor.user', 'photos', 'subcategory.category']);
    }

    /**
     * Update product status (admin only).
     */
    public function updateStatus(Product $product, string $status): Product
    {
        $product->update(['status' => $status]);

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $product->fresh(['vendor.user', 'photos', 'subcategory.category']);
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

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $product->fresh(['vendor.user', 'photos', 'subcategory.category']);
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

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $photos;
    }

    /**
     * Remove a single photo by ID.
     */
    public function removePhoto(ProductPhoto $photo): void
    {
        $product = $photo->product;
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        // Invalidate cache
        $this->invalidateProductCache($product);
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
            if ($firstRemaining instanceof ProductPhoto) {
                $firstRemaining->update(['is_primary' => true]);
            }
        }

        // Invalidate cache
        $this->invalidateProductCache($product);

        return $photos->count();
    }

    /**
     * Invalidate product-related cache.
     */
    protected function invalidateProductCache(Product $product, ?int $vendorId = null): void
    {
        $vendorId = $vendorId ?? $product->vendor_id;

        // Clear all paginated product caches (public) - clear first 10 pages
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("products:public:list:page:{$i}");
            Cache::forget("products:public:vendor:{$vendorId}:page:{$i}");
            Cache::forget("products:public:page:{$i}");
        }

        // Clear product detail cache
        Cache::forget("products:public:{$product->id}:details");
        Cache::forget("products:{$product->id}:details");

        // Clear vendor cache if vendor exists
        if ($vendorId) {
            Cache::forget("vendors:{$vendorId}:details");
        }

        // Clear vendor list cache
        Cache::forget('vendors:active:list');
    }
}
