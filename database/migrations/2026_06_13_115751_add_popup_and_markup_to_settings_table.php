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
            if (!Schema::hasColumn('settings', 'default_member_markup')) {
                $table->decimal('default_member_markup', 15, 2)->default(2000)->after('value');
            }
            if (!Schema::hasColumn('settings', 'default_reseller_markup')) {
                $table->decimal('default_reseller_markup', 15, 2)->default(1000)->after('default_member_markup');
            }
            if (!Schema::hasColumn('settings', 'popup_active')) {
                $table->boolean('popup_active')->default(false);
            }
            if (!Schema::hasColumn('settings', 'popup_title')) {
                $table->string('popup_title')->nullable();
            }
            if (!Schema::hasColumn('settings', 'popup_image')) {
                $table->string('popup_image')->nullable();
            }
            if (!Schema::hasColumn('settings', 'popup_text')) {
                $table->text('popup_text')->nullable();
            }
            if (!Schema::hasColumn('settings', 'popup_button_text')) {
                $table->string('popup_button_text')->nullable();
            }
            if (!Schema::hasColumn('settings', 'popup_button_color')) {
                $table->string('popup_button_color')->default('#ffffff');
            }
            if (!Schema::hasColumn('settings', 'popup_button_bg_color')) {
                $table->string('popup_button_bg_color')->default('#3b82f6');
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
                'default_member_markup',
                'default_reseller_markup',
                'popup_active',
                'popup_title',
                'popup_image',
                'popup_text',
                'popup_button_text',
                'popup_button_color',
                'popup_button_bg_color'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
