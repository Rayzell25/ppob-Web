<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_provider_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('provider_sku'); // SKU used by provider API
            $table->bigInteger('cost_price')->default(0); // Cost price from provider
            $table->integer('priority')->default(1); // Routing priority (1 = highest)
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active'); // active, inactive, offline
            $table->timestamps();

            // Indexing for quick routing lookup
            $table->index(['product_id', 'is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_provider_routes');
    }
};
