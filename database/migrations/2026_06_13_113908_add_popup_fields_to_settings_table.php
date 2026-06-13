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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('popup_active')->default(false);
            $table->string('popup_title')->nullable();
            $table->string('popup_image')->nullable();
            $table->text('popup_text')->nullable();
            $table->string('popup_button_text')->nullable();
            $table->string('popup_button_color')->nullable();
            $table->string('popup_button_bg_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'popup_active',
                'popup_title',
                'popup_image',
                'popup_text',
                'popup_button_text',
                'popup_button_color',
                'popup_button_bg_color'
            ]);
        });
    }
};
