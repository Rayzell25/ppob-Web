<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductMarkupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some global settings
        Setting::updateOrCreate(['key' => 'default_member_markup'], ['value' => '2000']);
        Setting::updateOrCreate(['key' => 'default_reseller_markup'], ['value' => '1000']);
    }

    /** @test */
    public function it_uses_product_specific_markup_first()
    {
        $category = Category::create([
            'name' => 'Pulsa',
            'slug' => 'pulsa',
            'member_markup' => 1500,
            'reseller_markup' => 800,
        ]);

        $brand = Brand::create([
            'category_id' => $category->id,
            'name' => 'Telkomsel',
            'slug' => 'telkomsel',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'provider' => 'digiflazz',
            'product_code' => 'T10K',
            'sku' => 'T10K',
            'name' => 'Telkomsel 10K',
            'base_price' => 10000,
            'member_markup' => 500,
            'reseller_markup' => 300,
        ]);

        $this->assertEquals(10500, $product->getFinalPrice('member'));
        $this->assertEquals(10300, $product->getFinalPrice('reseller'));
    }

    /** @test */
    public function it_falls_back_to_category_markup_if_product_markup_is_null()
    {
        $category = Category::create([
            'name' => 'Pulsa',
            'slug' => 'pulsa',
            'member_markup' => 1500,
            'reseller_markup' => 800,
        ]);

        $brand = Brand::create([
            'category_id' => $category->id,
            'name' => 'Telkomsel',
            'slug' => 'telkomsel',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'provider' => 'digiflazz',
            'product_code' => 'T10K',
            'sku' => 'T10K',
            'name' => 'Telkomsel 10K',
            'base_price' => 10000,
            'member_markup' => null,
            'reseller_markup' => null,
        ]);

        $this->assertEquals(11500, $product->getFinalPrice('member'));
        $this->assertEquals(10800, $product->getFinalPrice('reseller'));
    }

    /** @test */
    public function it_falls_back_to_global_default_markup_if_both_product_and_category_markup_are_null()
    {
        $category = Category::create([
            'name' => 'Pulsa',
            'slug' => 'pulsa',
            'member_markup' => null,
            'reseller_markup' => null,
        ]);

        $brand = Brand::create([
            'category_id' => $category->id,
            'name' => 'Telkomsel',
            'slug' => 'telkomsel',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'provider' => 'digiflazz',
            'product_code' => 'T10K',
            'sku' => 'T10K',
            'name' => 'Telkomsel 10K',
            'base_price' => 10000,
            'member_markup' => null,
            'reseller_markup' => null,
        ]);

        $this->assertEquals(12000, $product->getFinalPrice('member'));
        $this->assertEquals(11000, $product->getFinalPrice('reseller'));
    }
}
