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
        'provider_id',
        'product_code',
        'name',
        'sku',
        'description',
        'base_price',
        'provider_server_price',
        'price',
        'member_markup',
        'reseller_markup',
        'type',
        'status',
        'is_active',
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
            'base_price' => 'decimal:2',
            'provider_server_price' => 'decimal:2',
            'member_markup' => 'decimal:2',
            'reseller_markup' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Calculate the final price based on hierarchical markups.
     *
     * @param string $role
     * @return float
     */
    public function getFinalPrice($role = 'member')
    {
        $setting = \App\Models\Setting::pluck('value', 'key');
        if ($role === 'reseller') {
            $markup = $this->reseller_markup ?? $this->category->reseller_markup ?? ($setting['default_reseller_markup'] ?? 1000);
        } else {
            $markup = $this->member_markup ?? $this->category->member_markup ?? ($setting['default_member_markup'] ?? 2000);
        }
        return $this->base_price + $markup;
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
     * Get the direct API provider assigned to this product (overrides category default).
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
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
