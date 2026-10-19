<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN; rebuild the table if needed
        if (DB::getDriverName() === 'sqlite') {
            // Check if focus_rating is already nullable by testing
            try {
                DB::statement("UPDATE daily_logs SET focus_rating = NULL WHERE id = -999");
            } catch (\Exception $e) {
                // Column is NOT NULL — rebuild table
                DB::statement("CREATE TABLE daily_logs_temp AS SELECT * FROM daily_logs");
                DB::statement("DROP TABLE daily_logs");
                DB::statement("
                    CREATE TABLE daily_logs (
                        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                        date DATE NOT NULL UNIQUE,
                        focus_rating INTEGER DEFAULT NULL,
                        notes TEXT,
                        daily_goal VARCHAR(255) DEFAULT NULL,
                        key_tasks TEXT DEFAULT NULL,
                        selected_step_ids TEXT DEFAULT NULL,
                        energy_level INTEGER DEFAULT NULL,
                        mood INTEGER DEFAULT NULL,
                        distractions TEXT DEFAULT NULL,
                        sleep_hours NUMERIC DEFAULT NULL,
                        sleep_quality INTEGER DEFAULT NULL,
                        stress_level INTEGER DEFAULT NULL,
                        training_done BOOLEAN DEFAULT 0 NOT NULL,
                        recovery_quality INTEGER DEFAULT NULL,
                        nutrition_quality INTEGER DEFAULT NULL,
                        created_at DATETIME DEFAULT NULL,
                        updated_at DATETIME DEFAULT NULL
                    )
                ");
                DB::statement("INSERT INTO daily_logs SELECT * FROM daily_logs_temp");
                DB::statement("DROP TABLE daily_logs_temp");
                DB::statement("CREATE INDEX daily_logs_date_index ON daily_logs (date)");
            }
        } else {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->tinyInteger('focus_rating')->nullable()->default(null)->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Restore NOT NULL with default 0
            DB::statement("UPDATE daily_logs SET focus_rating = 0 WHERE focus_rating IS NULL");
            DB::statement("CREATE TABLE daily_logs_temp AS SELECT * FROM daily_logs");
            DB::statement("DROP TABLE daily_logs");
            DB::statement("
                CREATE TABLE daily_logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    date DATE NOT NULL UNIQUE,
                    focus_rating TINYINT NOT NULL DEFAULT 0,
                    notes TEXT,
                    daily_goal VARCHAR(255) DEFAULT NULL,
                    key_tasks TEXT DEFAULT NULL,
                    selected_step_ids TEXT DEFAULT NULL,
                    energy_level INTEGER DEFAULT NULL,
                    mood INTEGER DEFAULT NULL,
                    distractions TEXT DEFAULT NULL,
                    sleep_hours NUMERIC DEFAULT NULL,
                    sleep_quality INTEGER DEFAULT NULL,
                    stress_level INTEGER DEFAULT NULL,
                    training_done BOOLEAN DEFAULT 0 NOT NULL,
                    recovery_quality INTEGER DEFAULT NULL,
                    nutrition_quality INTEGER DEFAULT NULL,
                    created_at DATETIME DEFAULT NULL,
                    updated_at DATETIME DEFAULT NULL
                )
            ");
            DB::statement("INSERT INTO daily_logs SELECT * FROM daily_logs_temp");
            DB::statement("DROP TABLE daily_logs_temp");
            DB::statement("CREATE INDEX daily_logs_date_index ON daily_logs (date)");
        } else {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->tinyInteger('focus_rating')->nullable(false)->default(0)->change();
            });
        }
    }
};
