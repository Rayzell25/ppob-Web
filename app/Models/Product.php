<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'sku',
        'description',
        'price',
        'type',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the cascading provider routing entries.
     */
    public function providerRoutes(): HasMany
    {
        return $this->hasMany(ProductProviderRoute::class)->orderBy('priority', 'asc');
    }

    /**
     * Get the providers linked to the product.
     */
    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'product_provider_routes')
            ->withPivot(['id', 'provider_sku', 'cost_price', 'priority', 'is_active', 'status'])
            ->withTimestamps();
    }

    /**
     * Get the transactions for the product.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the primary active provider route.
     */
    public function getActiveProvider()
    {
        return $this->providerRoutes()
            ->with('provider')
            ->where('is_active', true)
            ->where('status', 'active')
            ->first();
    }
}
