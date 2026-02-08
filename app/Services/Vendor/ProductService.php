<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get paginated products for the given vendor with first photo (optimized for list).
     */
    public function listForVendor(Vendor $vendor, int $perPage = 15): LengthAwarePaginator
    {
        return $vendor->products()
            ->with(['photos' => function ($query) {
                $query->orderBy('sort_order')->limit(1);
            }])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a product for the given vendor.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(Vendor $vendor, array $data): Product
    {
        $product = $vendor->products()->create($data);

        return $product->load('photos');
    }

    /**
     * Update an existing product.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh('photos');
    }

    /**
     * Delete a product and its photos from storage.
     */
    public function delete(Product $product): void
    {
        $product->delete();
    }
}
