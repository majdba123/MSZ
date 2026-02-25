<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class VendorController extends Controller
{
    /**
     * List active vendors (cached with Redis tags).
     */
    public function index(): JsonResponse
    {
        try {
            $vendors = Cache::tags(['vendors'])->remember('active_vendors', 1800, function () {
                return Vendor::query()
                    ->where('is_active', true)
                    ->with('user:id,name')
                    ->latest()
                    ->limit(20)
                    ->get(['id', 'store_name', 'description', 'logo', 'user_id']);
            });
        } catch (\Exception $e) {
            $vendors = Vendor::query()
                ->where('is_active', true)
                ->with('user:id,name')
                ->latest()
                ->limit(20)
                ->get(['id', 'store_name', 'description', 'logo', 'user_id']);
        }

        return response()->json([
            'message' => __('Vendors retrieved successfully.'),
            'data' => $vendors->map(fn ($v) => [
                'id' => $v->id,
                'store_name' => $v->store_name,
                'description' => $v->description,
                'logo' => $v->logo ? asset('storage/'.$v->logo) : null,
                'user' => $v->user ? ['id' => $v->user->id, 'name' => $v->user->name] : null,
            ]),
        ]);
    }

    /**
     * Show vendor details (cached with Redis tags).
     */
    public function show(Vendor $vendor): JsonResponse
    {
        if (! $vendor->is_active) {
            abort(404, __('Vendor not found.'));
        }

        try {
            $vendorData = Cache::tags(['vendors'])->remember("vendor:{$vendor->id}", 1800, function () use ($vendor) {
                $vendor->load('user:id,name');

                return [
                    'id' => $vendor->id,
                    'store_name' => $vendor->store_name,
                    'description' => $vendor->description,
                    'address' => $vendor->address,
                    'logo' => $vendor->logo ? asset('storage/'.$vendor->logo) : null,
                    'user' => $vendor->user ? ['id' => $vendor->user->id, 'name' => $vendor->user->name] : null,
                ];
            });
        } catch (\Exception $e) {
            $vendor->load('user:id,name');
            $vendorData = [
                'id' => $vendor->id,
                'store_name' => $vendor->store_name,
                'description' => $vendor->description,
                'address' => $vendor->address,
                'logo' => $vendor->logo ? asset('storage/'.$vendor->logo) : null,
                'user' => $vendor->user ? ['id' => $vendor->user->id, 'name' => $vendor->user->name] : null,
            ];
        }

        return response()->json([
            'message' => __('Vendor retrieved successfully.'),
            'data' => $vendorData,
        ]);
    }
}
