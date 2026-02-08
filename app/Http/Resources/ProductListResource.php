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
        $firstPhoto = $this->whenLoaded('photos') ? $this->photos->first() : null;

            return [
                'id' => $this->id,
                'vendor_id' => $this->vendor_id,
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'quantity' => $this->quantity,
                'is_active' => $this->is_active,
                'status' => $this->status,
                'first_photo_url' => $firstPhoto ? asset('storage/'.$firstPhoto->path) : null,
            ];
    }
}
