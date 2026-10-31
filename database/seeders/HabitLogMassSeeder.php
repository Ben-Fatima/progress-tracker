<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds ~1,000,000 habit_logs rows for the index-demo task.
 * unique(habit_id, date) allows only one row per habit per day,
 * so volume comes from habits x days.
 *
 * Two populations:
 *  - 100 active habits x ~4000 recent days: feeds the dashboard and the
 *    Oct-15 summary count (stays 100).
 *  - 50000 INACTIVE habits x 20 old days only: invisible to the dashboard
 *    (active() scope) and never logged near Oct-15, but they raise the
 *    distinct habit_id count high enough that SQLite's skip-scan on the
 *    (habit_id, date) index becomes unattractive — so the date-only filter
 *    shows a real SCAN in EXPLAIN.
 */
class HabitLogMassSeeder extends Seeder
{
    public function run(): void
    {
        $activeCount = DB::table('habits')->where('active', true)->count();
        for ($i = $activeCount; $i < 100; $i++) {
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

        $inactiveCount = DB::table('habits')->where('active', false)->count();
        for ($i = $inactiveCount; $i < 50000; $i++) {
            DB::table('habits')->insert([
                'name' => 'Old Habit ' . ($i + 1),
                'icon' => 'x',
                'frequency' => 'daily',
                'sort_order' => 1000 + $i,
                'active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $activeIds = DB::table('habits')->where('active', true)->pluck('id')->all();
        $inactiveIds = DB::table('habits')->where('active', false)->pluck('id')->all();
        $now = now();
        $chunk = [];

        $flush = function () use (&$chunk) {
            if ($chunk) {
                DB::table('habit_logs')->insertOrIgnore($chunk);
                $chunk = [];
            }
        };

        // Recent history for visible habits. Skip day 45 so streaks cap at
        // ~45 days — keeps the per-habit streak loop cheap.
        for ($d = 0; $d < 4000; $d++) {
            if ($d === 45) {
                continue;
            }
            $date = date('Y-m-d', strtotime('-' . $d . ' days'));
            foreach ($activeIds as $habitId) {
                $chunk[] = [
                    'habit_id' => $habitId,
                    'date' => $date,
                    'completed' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($chunk) === 5000) {
                    $flush();
                }
            }
        }

        // Old history for inactive habits — starts 4000 days back, so none
        // of it can land on the summary date and inflate its count.
        for ($d = 4000; $d < 4020; $d++) {
            $date = date('Y-m-d', strtotime('-' . $d . ' days'));
            foreach ($inactiveIds as $habitId) {
                $chunk[] = [
                    'habit_id' => $habitId,
                    'date' => $date,
                    'completed' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($chunk) === 5000) {
                    $flush();
                }
            }
        }
        $flush();
    }
}
