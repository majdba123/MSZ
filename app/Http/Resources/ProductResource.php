<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\VendorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'category_id' => $this->subcategory?->category_id,
            'subcategory_id' => $this->subcategory_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'category' => $this->whenLoaded('subcategory', function (): ?array {
                $category = $this->subcategory?->category;

                return $category ? [
                    'id' => $category->id,
                    'name' => $category->name,
                    'commission' => $category->commission,
                ] : null;
            }),
            'subcategory' => $this->whenLoaded('subcategory', function (): ?array {
                $subcategory = $this->subcategory;

                return $subcategory ? [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'category_id' => $subcategory->category_id,
                ] : null;
            }),
            'photos' => ProductPhotoResource::collection($this->whenLoaded('photos')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
