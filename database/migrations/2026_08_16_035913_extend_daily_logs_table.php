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
            $table->string('daily_goal')->nullable()->after('date');
            $table->json('key_tasks')->nullable()->after('daily_goal');
            $table->tinyInteger('energy_level')->nullable()->after('focus_rating');
            $table->tinyInteger('mood')->nullable()->after('energy_level');
            $table->text('distractions')->nullable()->after('mood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn(['daily_goal', 'key_tasks', 'energy_level', 'mood', 'distractions']);
        });
    }
};
