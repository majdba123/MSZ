<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductPhotoResource;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Services\ProductPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductPhotoController extends Controller
{
    public function __construct(public ProductPhotoService $photoService) {}

    /**
     * List photos for a product.
     */
    public function index(Product $product): JsonResponse
    {
        $product->load('photos');

        return response()->json([
            'message' => __('Photos retrieved successfully.'),
            'data' => ProductPhotoResource::collection($product->photos),
        ]);
    }

    /**
     * Upload photos to a product.
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ]);

        $photos = $this->photoService->addPhotos($product, $request->file('photos'));

        return response()->json([
            'message' => __(':count photo(s) uploaded successfully.', ['count' => count($photos)]),
            'data' => ProductPhotoResource::collection($photos),
        ], 201);
    }

    /**
     * Remove a single photo.
     */
    public function destroy(Product $product, ProductPhoto $photo): JsonResponse
    {
        if ($photo->product_id !== $product->id) {
            abort(404);
        }

        $this->photoService->removePhoto($photo);

        return response()->json([
            'message' => __('Photo deleted successfully.'),
        ]);
    }

    /**
     * Bulk-remove photos by IDs.
     */
    public function bulkDestroy(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'photo_ids' => ['required', 'array', 'min:1'],
            'photo_ids.*' => ['required', 'integer', 'exists:product_photos,id'],
        ]);

        $count = $this->photoService->removePhotos($product, $request->input('photo_ids'));

        return response()->json([
            'message' => __(':count photo(s) deleted successfully.', ['count' => $count]),
        ]);
    }
}
