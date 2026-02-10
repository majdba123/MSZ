<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendorService
{
    /**
     * Create a vendor along with its user account.
     *
     * @param  array{name: string, email?: string, password: string, phone_number: string, national_id: string, store_name: string, description?: string, address?: string, logo?: string}  $data
     */
    public function create(array $data): Vendor
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'password' => $data['password'],
                'phone_number' => $data['phone_number'],
                'national_id' => $data['national_id'],
                'type' => User::TYPE_VENDOR,
            ]);

            $vendor = Vendor::query()->create([
                'user_id' => $user->id,
                'store_name' => $data['store_name'],
                'description' => $data['description'] ?? null,
                'address' => $data['address'] ?? null,
                'logo' => $data['logo'] ?? null,
            ]);

            // Invalidate vendor cache
            Cache::forget('vendors:active:list');

            return $vendor;
        });
    }

    /**
     * Update an existing vendor and its user account.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Vendor $vendor, array $data): Vendor
    {
        return DB::transaction(function () use ($vendor, $data) {
            // Update user fields if provided
            $userFields = array_filter(
                array_intersect_key($data, array_flip(['name', 'email', 'password', 'phone_number', 'national_id'])),
                fn ($value) => $value !== null,
            );

            if ($userFields) {
                $vendor->user->update($userFields);
            }

            // Update vendor fields
            $vendorFields = array_intersect_key($data, array_flip([
                'store_name', 'description', 'address', 'logo', 'is_active',
            ]));

            if ($vendorFields) {
                $vendor->update($vendorFields);
            }

            return $vendor->fresh('user');
        });
    }

    /**
     * Toggle vendor active status.
     */
    public function toggleActive(Vendor $vendor): Vendor
    {
        $vendor->update(['is_active' => ! $vendor->is_active]);

        // Invalidate vendor cache
        Cache::forget("vendors:{$vendor->id}:details");
        Cache::forget('vendors:active:list');

        // Invalidate product caches for this vendor
        Cache::forget("products:public:vendor:{$vendor->id}:page:1");

        return $vendor->fresh('user');
    }

    /**
     * Delete a vendor and its user account.
     */
    public function delete(Vendor $vendor): void
    {
        DB::transaction(function () use ($vendor) {
            $vendorId = $vendor->id;
            $vendor->user->tokens()->delete();
            $vendor->delete();
            $vendor->user->delete();

            // Invalidate vendor cache
            Cache::forget("vendors:{$vendorId}:details");
            Cache::forget('vendors:active:list');

            // Invalidate product caches for this vendor
            Cache::forget("products:public:vendor:{$vendorId}:page:1");
        });
    }
}
