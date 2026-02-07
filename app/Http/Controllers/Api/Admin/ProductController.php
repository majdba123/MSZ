<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Admin\ProductService;
use App\Services\ProductPhotoService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        public ProductService $productService,
        public ProductPhotoService $photoService,
    ) {}

    /**
     * List all products (slim response).
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->list();

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

    /**
     * Show a single product.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['vendor.user', 'photos']);

        return response()->json([
            'message' => __('Product retrieved successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Create a product with optional photos (single multipart request).
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $photos = $request->file('photos', []);
        unset($validated['photos']);

        $product = $this->productService->create($validated);

        if (! empty($photos)) {
            $this->photoService->addPhotos($product, $photos);
            $product->load('photos');
        }

        return response()->json([
            'message' => __('Product created successfully.'),
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * Update a product (no photo handling here — use photo API).
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());

        return response()->json([
            'message' => __('Product updated successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Toggle product active status.
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
     * Delete a product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json([
            'message' => __('Product deleted successfully.'),
        ]);
    }
}
