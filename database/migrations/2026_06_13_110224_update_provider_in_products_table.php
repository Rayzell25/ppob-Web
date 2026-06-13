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
        Schema::table('products', function (Blueprint $table) {
            // Drop the old string-based provider column
            $table->dropColumn('provider');
        });

        Schema::table('products', function (Blueprint $table) {
            // Add new FK-based provider_id and server price column
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete()->after('brand_id');
            $table->decimal('provider_server_price', 15, 2)->default(0)->after('base_price')->comment('Harga asli dari server API');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropColumn(['provider_id', 'provider_server_price']);
            $table->string('provider')->nullable()->after('brand_id');
        });
    }
};
