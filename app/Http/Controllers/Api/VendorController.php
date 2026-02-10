<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class VendorController extends Controller
{
    /**
     * List active vendors (cached).
     */
    public function index(): JsonResponse
    {
        try {
            $vendors = Cache::remember('vendors:active:list', 3600, function () {
                return Vendor::query()
                    ->where('is_active', true)
                    ->with('user:id,name')
                    ->latest()
                    ->limit(10)
                    ->get(['id', 'store_name', 'description', 'logo', 'user_id']);
            });
        } catch (\Exception $e) {
            // Fallback if cache fails
            $vendors = Vendor::query()
                ->where('is_active', true)
                ->with('user:id,name')
                ->latest()
                ->limit(10)
                ->get(['id', 'store_name', 'description', 'logo', 'user_id']);
        }

        return response()->json([
            'message' => __('Vendors retrieved successfully.'),
            'data' => $vendors->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'store_name' => $vendor->store_name,
                    'description' => $vendor->description,
                    'logo' => $vendor->logo ? asset('storage/'.$vendor->logo) : null,
                    'user' => $vendor->user ? [
                        'id' => $vendor->user->id,
                        'name' => $vendor->user->name,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * Show vendor details (cached).
     */
    public function show(Vendor $vendor): JsonResponse
    {
        if (! $vendor->is_active) {
            abort(404, __('Vendor not found.'));
        }

        try {
            $vendorData = Cache::remember("vendors:{$vendor->id}:details", 3600, function () use ($vendor) {
                $vendor->load('user:id,name');

                return [
                    'id' => $vendor->id,
                    'store_name' => $vendor->store_name,
                    'description' => $vendor->description,
                    'address' => $vendor->address,
                    'logo' => $vendor->logo ? asset('storage/'.$vendor->logo) : null,
                    'user' => $vendor->user ? [
                        'id' => $vendor->user->id,
                        'name' => $vendor->user->name,
                    ] : null,
                ];
            });
        } catch (\Exception $e) {
            // Fallback if cache fails
            $vendor->load('user:id,name');
            $vendorData = [
                'id' => $vendor->id,
                'store_name' => $vendor->store_name,
                'description' => $vendor->description,
                'address' => $vendor->address,
                'logo' => $vendor->logo ? asset('storage/'.$vendor->logo) : null,
                'user' => $vendor->user ? [
                    'id' => $vendor->user->id,
                    'name' => $vendor->user->name,
                ] : null,
            ];
        }

        return response()->json([
            'message' => __('Vendor retrieved successfully.'),
            'data' => $vendorData,
        ]);
    }
}
