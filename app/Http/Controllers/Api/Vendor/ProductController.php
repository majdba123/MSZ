<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreProductRequest;
use App\Http\Requests\Vendor\UpdateProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductPhotoService;
use App\Services\Vendor\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        public ProductService $productService,
        public ProductPhotoService $photoService,
    ) {}

    /**
     * List the authenticated vendor's products (slim response).
     */
    public function index(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendor;
        $products = $this->productService->listForVendor($vendor);

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
     * Show a single product (scoped to vendor).
     */
    public function show(Request $request, Product $product): JsonResponse
    {
        $this->authorizeVendorOwnership($request, $product);

        $product->load('photos');

        return response()->json([
            'message' => __('Product retrieved successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Create a new product with optional photos (single multipart request).
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $vendor = $request->user()->vendor;
        $validated = $request->validated();
        $photos = $request->file('photos', []);
        unset($validated['photos']);

        $product = $this->productService->create($vendor, $validated);

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
     * Update a vendor's product (no photo handling here).
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorizeVendorOwnership($request, $product);

        $product = $this->productService->update($product, $request->validated());

        return response()->json([
            'message' => __('Product updated successfully.'),
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Delete a vendor's product.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->authorizeVendorOwnership($request, $product);

        $this->productService->delete($product);

        return response()->json([
            'message' => __('Product deleted successfully.'),
        ]);
    }

    /**
     * Ensure the product belongs to the authenticated vendor.
     */
    private function authorizeVendorOwnership(Request $request, Product $product): void
    {
        if ($product->vendor_id !== $request->user()->vendor->id) {
            abort(403, __('You do not own this product.'));
        }
    }
}
