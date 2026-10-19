<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add energy_mode to daily_logs
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->string('energy_mode', 10)->nullable()->after('date'); // low, normal, high, peak
        });

        // Add feedback fields to focus_sessions
        Schema::table('focus_sessions', function (Blueprint $table) {
            $table->integer('step_id')->nullable()->after('category_id');
            $table->string('outcome', 20)->nullable()->after('duration_minutes'); // completed, progress, blocked, distracted
            $table->integer('difficulty')->nullable()->after('outcome'); // 1-5
            $table->integer('xp_earned')->default(0)->after('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn('energy_mode');
        });

        Schema::table('focus_sessions', function (Blueprint $table) {
            $table->dropColumn(['step_id', 'outcome', 'difficulty', 'xp_earned']);
        });
    }
};
