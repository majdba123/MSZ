<?php

namespace App\Services\Admin;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get paginated products (no eager loading — slim list).
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a product assigned to a vendor.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        $product = Product::query()->create($data);

        return $product->load(['vendor.user', 'photos']);
    }

    /**
     * Update an existing product (no vendor_id change).
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(['vendor.user', 'photos']);
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): void
    {
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
}
