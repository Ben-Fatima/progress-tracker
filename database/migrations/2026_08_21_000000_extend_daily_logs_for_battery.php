<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->decimal('sleep_hours', 4, 1)->nullable()->after('distractions');
            $table->tinyInteger('sleep_quality')->nullable()->after('sleep_hours');
            $table->tinyInteger('stress_level')->nullable()->after('sleep_quality');
            $table->boolean('training_done')->default(false)->after('stress_level');
            $table->tinyInteger('recovery_quality')->nullable()->after('training_done');
            $table->tinyInteger('nutrition_quality')->nullable()->after('recovery_quality');
        });
    }

    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn([
                'sleep_hours', 'sleep_quality', 'stress_level',
                'training_done', 'recovery_quality', 'nutrition_quality',
            ]);
        });
    }
};
