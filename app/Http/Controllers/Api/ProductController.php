<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest as AdminStoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest as AdminUpdateProductRequest;
use App\Http\Requests\Vendor\StoreProductRequest as VendorStoreProductRequest;
use App\Http\Requests\Vendor\UpdateProductRequest as VendorUpdateProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct(
        public ProductService $productService,
    ) {}

    /**
     * List products (slim response) with optional filters.
     * Admin: all products, can filter by vendor_id and is_active.
     * Vendor: only their products, can filter by is_active.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $vendor = null;

        // If vendor, scope to their products
        if ($user && $user->type === User::TYPE_VENDOR) {
            $vendor = $user->vendor;
            if (! $vendor) {
                abort(403, __('Vendor profile not found.'));
            }
            // Allow status and is_active filters for vendors
            $filters = $request->only(['status', 'is_active']);
        } else {
            // Admin: can filter by vendor_id, status, and is_active
            $filters = $request->only(['vendor_id', 'status', 'is_active']);
        }

        $products = $this->productService->list($vendor, 15, $filters);

        return response()->json([
            'message' => __('Products retrieved successfully.'),
            'data' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function publicIndex(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['vendor_id']);
            $vendorId = $filters['vendor_id'] ?? null;
            $page = $request->get('page', 1);

            // Cache key includes vendor filter and page
            $cacheKey = $vendorId
                ? "products:public:list:vendor:{$vendorId}:page:{$page}"
                : "products:public:list:page:{$page}";

            try {
                $response = Cache::remember($cacheKey, 1800, function () use ($filters) {
                    $products = $this->productService->listPublic(15, $filters);

                    return [
                        'data' => ProductListResource::collection($products),
                        'meta' => [
                            'current_page' => $products->currentPage(),
                            'last_page' => $products->lastPage(),
                            'per_page' => $products->perPage(),
                            'total' => $products->total(),
                        ],
                    ];
                });
            } catch (\Exception $cacheException) {
                // Fallback if cache fails
                $products = $this->productService->listPublic(15, $filters);
                $response = [
                    'data' => ProductListResource::collection($products),
                    'meta' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                    ],
                ];
            }

            return response()->json([
                'message' => __('Products retrieved successfully.'),
                ...$response,
            ]);
        } catch (\Exception $e) {
            Log::error('Public products index error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => __('An error occurred while retrieving products.'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show a public product (for clients/users).
     */
    public function publicShow(Product $product): JsonResponse
    {
        // Verify product is available for public viewing
        if (! $product->is_active || $product->status !== Product::STATUS_APPROVED || $product->quantity <= 0) {
            abort(404, __('Product not found.'));
        }

        if (! $product->vendor->is_active) {
            abort(404, __('Product not found.'));
        }

        // Cache product details
        $cacheKey = "products:public:{$product->id}:details";
        try {
            $productData = Cache::remember($cacheKey, 3600, function () use ($product) {
                $product->load(['vendor:id,store_name,user_id', 'vendor.user:id,name', 'photos']);

                return new ProductResource($product);
            });
        } catch (\Exception $e) {
            // Fallback if cache fails
            $product->load(['vendor:id,store_name,user_id', 'vendor.user:id,name', 'photos']);
            $productData = new ProductResource($product);
        }

        return response()->json([
            'message' => __('Product retrieved successfully.'),
            'data' => $productData,
        ]);
    }

    /**
     * Show a single product.
     * Admin: can view any product.
     * Vendor: can only view their own products.
     */
    public function show(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        // Vendor can only view their own products
        if ($user && $user->type === User::TYPE_VENDOR) {
            $vendor = $user->vendor;
            if (! $vendor) {
                abort(403, __('Vendor profile not found.'));
            }
            if ($product->vendor_id !== $vendor->id) {
                abort(403, __('You do not own this product.'));
            }
            $product->load('photos');
        } else {
            // Admin: load vendor relationship
            $product->load(['vendor.user', 'photos']);
        }

        // Ensure primary photo is set if photos exist but no primary is set
        if ($product->photos->isNotEmpty() && ! $product->photos->where('is_primary', true)->first()) {
            $firstPhoto = $product->photos->first();
            $firstPhoto->update(['is_primary' => true]);
            $product->refresh();
            $product->load('photos');
        }

        return response()->json([
            'message' => __('Product retrieved successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Create a product with optional photos.
     * Admin: must provide vendor_id.
     * Vendor: vendor_id is set automatically.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $vendor = null;

        // Determine which request class to use and validate
        if ($user && $user->type === User::TYPE_VENDOR) {
            $vendor = $user->vendor;
            if (! $vendor) {
                abort(403, __('Vendor profile not found.'));
            }
            $validated = $request->validate((new VendorStoreProductRequest)->rules());
        } else {
            $validated = $request->validate((new AdminStoreProductRequest)->rules());
        }

        // Convert is_active to boolean if present
        if (isset($validated['is_active'])) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        $photos = $request->file('photos', []);
        unset($validated['photos']);

        $product = $this->productService->create($vendor, $validated);

        if (! empty($photos)) {
            $this->productService->addPhotos($product, $photos);
            $product->load('photos');
        }

        return response()->json([
            'message' => __('Product created successfully.'),
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * Update a product.
     * Admin: can update any product.
     * Vendor: can only update their own products.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        // Vendor can only update their own products
        if ($user && $user->type === User::TYPE_VENDOR) {
            $vendor = $user->vendor;
            if (! $vendor) {
                abort(403, __('Vendor profile not found.'));
            }
            if ($product->vendor_id !== $vendor->id) {
                abort(403, __('You do not own this product.'));
            }
            $validated = $request->validate((new VendorUpdateProductRequest)->rules());
        } else {
            $validated = $request->validate((new AdminUpdateProductRequest)->rules());
        }

        // Convert is_active to boolean if present
        if (isset($validated['is_active'])) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        // Only admin can update status
        if (isset($validated['status'])) {
            if (! $user || $user->type !== User::TYPE_ADMIN) {
                unset($validated['status']); // Remove status from validated data for non-admins
            }
        }

        // Note: Photo updates (remove, upload, set primary) are handled separately via ProductPhotoController::updatePhotos

        $product = $this->productService->update($product, $validated);
        $product->load($user && $user->type === User::TYPE_VENDOR ? 'photos' : ['vendor.user', 'photos']);

        return response()->json([
            'message' => __('Product updated successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Toggle product active status (admin only).
     */
    public function toggleActive(Product $product): JsonResponse
    {
        $product = $this->productService->toggleActive($product);

        return response()->json([
            'message' => $product->is_active
                ? __('Product activated successfully.')
                : __('Product deactivated successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Update product status (admin only).
     */
    public function updateStatus(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
        ]);

        $product = $this->productService->updateStatus($product, $request->input('status'));

        return response()->json([
            'message' => __('Product status updated successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Set primary photo for a product.
     */
    public function setPrimaryPhoto(Request $request, Product $product, ProductPhoto $photo): JsonResponse
    {
        $this->productService->setPrimaryPhoto($product, $photo);

        return response()->json([
            'message' => __('Primary photo updated successfully.'),
            'data' => new ProductResource($product->fresh(['vendor.user', 'photos'])),
        ]);
    }

    /**
     * Delete a product.
     * Admin: can delete any product.
     * Vendor: can only delete their own products.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        // Vendor can only delete their own products
        if ($user && $user->type === User::TYPE_VENDOR) {
            $vendor = $user->vendor;
            if (! $vendor) {
                abort(403, __('Vendor profile not found.'));
            }
            if ($product->vendor_id !== $vendor->id) {
                abort(403, __('You do not own this product.'));
            }
        }

        $this->productService->delete($product);

        return response()->json([
            'message' => __('Product deleted successfully.'),
        ]);
    }
}
