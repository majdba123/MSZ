<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'logo',
    ];

    /**
     * Get the subcategories for this category.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }
}
