<?php

/**
 * Populate the 3-month planning calendar (step_plan_days) with the Q4 plan.
 *
 * Rules:
 *  - Keeps every plan day you placed manually; plans only steps with none yet.
 *  - Respects per-day capacity (90m weekday / 240m weekend) minus existing usage.
 *  - Steps split across days automatically; per-day minutes stored explicitly
 *    when a step is split, null when it fits in one day.
 *  - Windows follow the agreed plan: M1 Sep 29–Oct 26, M2 Oct 27–Nov 23, M3 Nov 24–Dec 21.
 *  - Deferred tracks (Webhook, Interview Prep, Visa, Relocation Platforms,
 *    Financial) are intentionally not placed.
 *
 * Run: php _seed_plan_days.php
 */

use App\Livewire\PlanningPage;
use App\Models\Step;
use App\Models\StepPlanDay;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$windows = [
    // category_id => [start, end, label]
    // Month 1 — fix Fikrify, portfolio shell, LinkedIn, branding, Hana film day
    2  => ['2026-09-29', '2026-10-12'],  // Fikrify — Finish & Deploy (SSL first)
    10 => ['2026-09-29', '2026-10-12'],  // LinkedIn — Profile Setup
    38 => ['2026-09-29', '2026-10-12'],  // Portfolio — Foundation & Content Model
    39 => ['2026-10-05', '2026-10-26'],  // Portfolio — Design System
    17 => ['2026-09-29', '2026-10-19'],  // YT — Branding & Identity

    // Month 2 — portfolio case studies + launch, YT Video 0
    40 => ['2026-10-27', '2026-11-09'],  // Portfolio — Production Work Case Studies
    41 => ['2026-10-27', '2026-11-16'],  // Portfolio — Engineering Project Case Studies
    42 => ['2026-11-10', '2026-11-23'],  // Portfolio — Launch & Integration
    21 => ['2026-10-27', '2026-11-09'],  // YT — Video 0: The Setup

    // Month 3 — YouTube main track + AI platforms applications
    16 => ['2026-11-24', '2026-12-21'],  // YT — Setup & Equipment (essentials)
    18 => ['2026-11-24', '2026-12-21'],  // YT — First 5 Videos (progress series)
    19 => ['2026-11-24', '2026-12-07'],  // YT — Content System
    7  => ['2026-11-24', '2026-12-21'],  // AI Platforms — applications + first assessment
];

$fmt = fn ($min) => $min >= 60 ? floor($min / 60).'h'.($min % 60 ? ' '.($min % 60).'m' : '') : $min.'m';
$today = CarbonImmutable::today();
$runStartedAt = now()->subSecond(); // rows created before this are manual
$usage = []; // date => minutes already committed
$placed = 0;
$skippedManual = 0;
$overflow = [];

// Preload capacity already consumed by your manual placements.
StepPlanDay::with('step.planDays')->get()->each(function ($d) use (&$usage) {
    $key = $d->date->toDateString();
    $usage[$key] = ($usage[$key] ?? 0) + ($d->minutes ?? $d->step->minutesForDay());
});

foreach ($windows as $categoryId => [$start, $end]) {
    $cat = \App\Models\Category::find($categoryId);
    if (! $cat) {
        echo "!! missing category {$categoryId}\n";

        continue;
    }

    $steps = Step::where('category_id', $categoryId)
        ->where('status', '!=', 'done')
        ->whereDoesntHave('children')
        ->whereDoesntHave('planDays')
        ->orderBy('sort_order')
        ->get();

    $alreadyPlanned = Step::where('category_id', $categoryId)->whereHas('planDays')->count();
    $skippedManual += $alreadyPlanned;

    if ($steps->isEmpty()) {
        printf("%-50s nothing unplaced%s\n", mb_substr($cat->name, 0, 50), $alreadyPlanned ? " ({$alreadyPlanned} already planned)" : '');

        continue;
    }

    $startDate = CarbonImmutable::parse($start)->max($today);
    $endDate = CarbonImmutable::parse($end);

    // Spread evenly: each day gets a share of the work still unplaced,
    // capped by its free capacity. Prevents front-loading into week 1.
    $days = [];
    for ($d = $startDate; $d <= $endDate; $d = $d->addDay()) {
        $days[] = $d;
    }

    $placedHere = 0;
    $over = 0;
    $queue = $steps->map(fn ($s) => ['step' => $s, 'remaining' => $s->estimated_minutes ?: 30])->all();
    $remainingTotal = array_sum(array_column($queue, 'remaining'));

    foreach ($days as $i => $day) {
        $daysLeft = count($days) - $i;
        $key = $day->toDateString();
        $free = PlanningPage::capacityFor($day) - ($usage[$key] ?? 0);
        $budget = min($free, (int) ceil($remainingTotal / $daysLeft));

        while ($budget >= 15 && $queue) {
            $item = &$queue[0];
            $take = min($item['remaining'], $budget);
            if ($take < 15 && $item['remaining'] > 15) {
                break; // don't create slivers mid-split
            }
            StepPlanDay::create([
                'step_id' => $item['step']->id,
                'date' => $key,
                'minutes' => $take,
            ]);
            $usage[$key] = ($usage[$key] ?? 0) + $take;
            $item['remaining'] -= $take;
            $budget -= $take;
            $remainingTotal -= $take;
            $placedHere++;
            if ($item['remaining'] <= 0) {
                array_shift($queue);
            }
            unset($item);
        }
    }

    $over = array_sum(array_column($queue, 'remaining'));
    $placed += $placedHere;

    printf("%-50s %3d plan-days%s\n", mb_substr($cat->name, 0, 50), $placedHere,
        $over ? '  ⚠ '.$fmt($over).' overflowed past '.$endDate->format('M j') : '');
    if ($over) {
        $overflow[$cat->name] = $over;
    }
}

echo "\n=== Summary ===\n";
echo "  Plan-day rows created: {$placed}\n";
echo "  Steps skipped (already planned by you): {$skippedManual}\n";
echo '  Total scheduled in window: '.$fmt((int) StepPlanDay::all()->sum(fn ($d) => $d->minutes ?? $d->step->minutesForDay()))."\n";
if ($overflow) {
    echo "\n  ⚠ Overbooked categories (work exceeds the window):\n";
    foreach ($overflow as $name => $min) {
        echo "    {$name}: {$fmt($min)}\n";
    }
}
echo "Done!\n";
