<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'pomodoro_work_minutes', 'value' => '25', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pomodoro_break_minutes', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pomodoro_long_break_minutes', 'value' => '15', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pomodoro_blocks_before_long_break', 'value' => '4', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'theme', 'value' => 'light', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'daily_log_reminder_time', 'value' => '21:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
