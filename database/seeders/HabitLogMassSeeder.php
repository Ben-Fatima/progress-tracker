<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds ~1,000,000 habit_logs rows for the index-demo task.
 * unique(habit_id, date) allows only one row per habit per day,
 * so volume comes from habits x days: 100 habits x 10000 days.
 */
class HabitLogMassSeeder extends Seeder
{
    public function run(): void
    {
        $habits = DB::table('habits')->count();
        for ($i = $habits; $i < 100; $i++) {
            DB::table('habits')->insert([
                'name' => 'Habit ' . ($i + 1),
                'icon' => 'x',
                'frequency' => 'daily',
                'sort_order' => $i,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $habitIds = DB::table('habits')->pluck('id')->all();
        $now = now();
        $chunk = [];

        for ($d = 0; $d < 10000; $d++) {
            // Skip one day so streaks cap at ~45 days — keeps the per-habit
            // streak loop cheap no matter how far back the history goes.
            if ($d === 45) {
                continue;
            }
            $date = date('Y-m-d', strtotime('-' . $d . ' days'));
            foreach ($habitIds as $habitId) {
                $chunk[] = [
                    'habit_id' => $habitId,
                    'date' => $date,
                    'completed' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($chunk) === 5000) {
                    DB::table('habit_logs')->insertOrIgnore($chunk);
                    $chunk = [];
                }
            }
        }
        if ($chunk) {
            DB::table('habit_logs')->insertOrIgnore($chunk);
        }
    }
}
