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
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->integer('acne_level')->nullable()->after('nutrition_quality');
            $table->string('new_breakouts', 10)->nullable()->after('acne_level');
            $table->json('breakout_zones')->nullable()->after('new_breakouts');
            $table->boolean('am_skincare')->nullable()->after('breakout_zones');
            $table->boolean('sunscreen')->nullable()->after('am_skincare');
            $table->boolean('pm_skincare_last_night')->nullable()->after('sunscreen');
            $table->boolean('retinol_last_night')->nullable()->after('pm_skincare_last_night');
            $table->boolean('on_period')->nullable()->after('retinol_last_night');
            $table->string('skin_note')->nullable()->after('on_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn([
                'acne_level', 'new_breakouts', 'breakout_zones',
                'am_skincare', 'sunscreen', 'pm_skincare_last_night',
                'retinol_last_night', 'on_period', 'skin_note',
            ]);
        });
    }
};
