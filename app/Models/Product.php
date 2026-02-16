<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'subcategory_id',
        'name',
        'description',
        'price',
        'quantity',
        'is_active',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The vendor that owns this product.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * The photos for this product.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('sort_order');
    }

    /**
     * The primary photo for this product.
     */
    public function primaryPhoto(): ?ProductPhoto
    {
        return $this->photos()->where('is_primary', true)->first();
    }

    /**
     * The subcategory that owns this product.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
