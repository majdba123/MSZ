<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List orders with admin filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query()
            ->with([
                'user:id,name,email',
                'vendor:id,store_name',
                'items:id,order_id,product_id,product_name,quantity,line_total',
                'items.product:id,subcategory_id',
                'items.product.subcategory:id,name,category_id',
            ])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', (int) $request->input('vendor_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');
            $query->whereHas('items.product.subcategory', function ($builder) use ($categoryId) {
                $builder->where('category_id', $categoryId);
            });
        }

        if ($request->filled('subcategory_id')) {
            $query->whereHas('items.product', function ($builder) use ($request) {
                $builder->where('subcategory_id', (int) $request->input('subcategory_id'));
            });
        }

        if ($request->filled('product_id')) {
            $query->whereHas('items', function ($builder) use ($request) {
                $builder->where('product_id', (int) $request->input('product_id'));
            });
        }

        if ($request->filled('product')) {
            $term = trim((string) $request->input('product'));
            $query->whereHas('items', function ($builder) use ($term) {
                $builder->where('product_name', 'like', "%{$term}%");
            });
        }

        $orders = $query->paginate(12);

        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Show a single order for admin.
     */
    public function show(int $orderId): JsonResponse
    {
        $order = Order::query()
            ->with([
                'user:id,name,email',
                'vendor:id,store_name',
                'items:id,order_id,product_id,product_name,original_unit_price,has_discount,applied_discount_percentage,unit_price,quantity,line_total,discount_amount',
                'items.product:id,subcategory_id',
                'items.product.subcategory:id,name,category_id',
                'items.product.subcategory.category:id,name',
            ])
            ->findOrFail($orderId);

        return response()->json([
            'message' => 'Order retrieved successfully.',
            'data' => $order,
        ]);
    }
}
