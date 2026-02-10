<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Slim representation for product listing / index endpoints.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $photos = $this->whenLoaded('photos') ? $this->photos : collect();

        // Use primary photo if available, otherwise use first photo
        $displayPhoto = $photos->where('is_primary', true)->first() ?? $photos->first();

        $data = [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'first_photo_url' => $displayPhoto ? asset('storage/'.$displayPhoto->path) : null,
        ];

        // Include vendor info if loaded (for public listings)
        if ($this->whenLoaded('vendor')) {
            $vendor = $this->vendor;
            $data['vendor'] = [
                'id' => $vendor->id,
                'store_name' => $vendor->store_name,
                'user' => $vendor->relationLoaded('user') && $vendor->user ? [
                    'id' => $vendor->user->id,
                    'name' => $vendor->user->name,
                ] : null,
            ];
        }

        return $data;
    }
}
