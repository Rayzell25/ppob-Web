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
            if (!Schema::hasColumn('settings', 'footer_text')) {
                $table->text('footer_text')->nullable()->after('popup_button_bg_color');
                $table->string('social_instagram')->nullable()->after('footer_text');
                $table->string('social_telegram')->nullable()->after('social_instagram');
                $table->string('social_whatsapp')->nullable()->after('social_telegram');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [
                'footer_text',
                'social_instagram',
                'social_telegram',
                'social_whatsapp'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
