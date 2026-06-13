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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'topup', 'pembelian', 'deduction'
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('success'); // 'pending', 'success', 'failed'
            $table->text('description')->nullable();
            $table->string('reference')->nullable(); // optional link to PPOB transaction ref_id
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
