<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'type',
        'api_username',
        'api_key',
        'secret_key',
        'api_url',
        'base_url',
        'balance',
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
            'balance' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the routing options for this provider.
     */
    public function routes(): HasMany
    {
        return $this->hasMany(ProductProviderRoute::class);
    }

    /**
     * Get the transactions processed by this provider.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the products directly assigned to this provider.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the categories that default to this provider.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'default_provider_id');
    }
}
