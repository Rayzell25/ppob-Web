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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('target');
            $table->bigInteger('cost_price')->default(0);
            $table->bigInteger('sell_price')->default(0);
            $table->string('status')->default('pending'); // pending, processing, success, failed
            $table->string('serial_number')->nullable();
            $table->text('message')->nullable();
            $table->json('routing_attempts')->nullable(); // History of routing attempts for cascading audit
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('reference_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
