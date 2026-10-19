<?php

/**
 * Apply the agreed Q4 plan to the tracker.
 *
 *  1. Reset every deadline to the 3-month plan (Sep 29 -> Dec 21, 2026)
 *  2. Move "AI Platforms — Get Hired" out of Relocation to top-level (it is income, not relocation)
 *  3. Swap the FileImporter case-study steps for Fikrify ones in category 41
 *  4. Add an "OBS recording" step to every Portfolio category
 *
 * Nothing is archived: deferred categories just get realistic future deadlines.
 *
 * Run: php _seed_q4_plan.php
 */

use App\Models\Category;
use App\Models\Step;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// --- 1. Deadlines -----------------------------------------------------------
// Month 1: Sep 29 - Oct 26   Month 2: Oct 27 - Nov 23   Month 3: Nov 24 - Dec 21
$plan = [
    // id => [deadline, priority]
    // --- Active this quarter ---
    2 => ['2026-10-26', 'high'],     // Fikrify — Finish & Deploy (SSL first)
    10 => ['2026-10-12', 'high'],     // LinkedIn — Profile Setup
    37 => ['2026-11-23', 'high'],     // Portfolio Website — Rebuild (root)
    38 => ['2026-10-12', 'critical'], // Portfolio — Foundation & Content Model
    39 => ['2026-10-26', 'high'],     // Portfolio — Design System
    40 => ['2026-11-09', 'critical'], // Portfolio — Production Work Case Studies
    41 => ['2026-11-16', 'high'],     // Portfolio — Engineering Project Case Studies
    42 => ['2026-11-23', 'high'],     // Portfolio — Launch & Integration
    8 => ['2026-12-21', 'high'],     // Hana Atelier — Launch & Grow
    22 => ['2026-11-23', 'high'],     // Hana — Content Batch 1
    15 => ['2026-12-21', 'high'],     // YouTube Channel — Ores (root)
    17 => ['2026-10-19', 'high'],     // YT — Branding & Identity
    21 => ['2026-11-09', 'high'],     // YT — Video 0: The Setup
    16 => ['2026-11-23', 'normal'],   // YT — Setup & Equipment (essentials only)
    18 => ['2026-12-21', 'high'],     // YT — First 5 Videos (progress series)
    19 => ['2026-12-07', 'normal'],   // YT — Content System
    20 => ['2026-12-21', 'low'],      // YT — Growth & Evaluation
    7 => ['2026-12-21', 'high'],     // AI Platforms — Get Hired (income)

    // --- Deferred: real future deadlines instead of archiving ---
    9 => ['2027-04-22', 'normal'],   // Relocation NL/DE (root)
    4 => ['2027-02-28', 'low'],      // Webhook Delivery Service — Q1 project
    11 => ['2027-02-28', 'low'],      // Relocation Platforms — Get Visible
    12 => ['2027-03-31', 'low'],      // Interview Preparation — when interviews are real
    14 => ['2027-03-31', 'normal'],   // Financial Preparation
    13 => ['2027-04-30', 'low'],      // Visa & Documents — after an offer
];

echo "=== Deadlines ===\n";
foreach ($plan as $id => [$deadline, $priority]) {
    $cat = Category::find($id);
    if (! $cat) {
        echo "  !! missing category {$id}\n";

        continue;
    }
    $old = $cat->deadline?->format('Y-m-d') ?? 'none';
    $cat->update(['deadline' => $deadline, 'priority' => $priority]);
    printf("  %-52s %-10s -> %s (%s)\n", mb_substr($cat->name, 0, 52), $old, $deadline, $priority);
}

// --- 2. AI Platforms is income, not relocation ------------------------------
$ai = Category::find(7);
if ($ai && $ai->parent_id !== null) {
    $ai->update(['parent_id' => null, 'sort_order' => 7]);
    echo "\n=== Moved ===\n  {$ai->name} -> top-level\n";
}

// --- 3. Swap FileImporter case study for Fikrify (category 41) --------------
$eng = Category::findOrFail(41);
$removed = Step::where('category_id', $eng->id)->where('title', 'like', 'FileImporter:%')->delete();

$fikrify = [
    ['title' => 'Fikrify: write Problem & Constraints (no database, cost control, abuse prevention)', 'estimated_minutes' => 45, 'sort_order' => 5],
    ['title' => 'Fikrify: write the design (Redis rate limiting, URL-compressed stateless sharing, OpenAI orchestration)', 'estimated_minutes' => 60, 'sort_order' => 6],
    ['title' => 'Fikrify: write Tradeoffs (stateless vs persisted plans, IP throttling limits, prompt cost)', 'estimated_minutes' => 45, 'sort_order' => 7],
    ['title' => 'Fikrify: write "What I would change" + add architecture diagram', 'estimated_minutes' => 45, 'sort_order' => 8],
];

foreach ($fikrify as $step) {
    Step::create($step + ['category_id' => $eng->id]);
}

echo "\n=== Case study swap (category 41) ===\n";
echo "  removed {$removed} FileImporter steps, added ".count($fikrify)." Fikrify steps\n";

// --- 4. OBS capture step on every Portfolio category -----------------------
$portfolioCats = Category::where('parent_id', 37)->orderBy('sort_order')->get();
$obsTitle = 'Start OBS recording before each work session (bank YouTube footage)';

echo "\n=== OBS capture steps ===\n";
foreach ($portfolioCats as $cat) {
    if (Step::where('category_id', $cat->id)->where('title', $obsTitle)->exists()) {
        echo "  already present: {$cat->name}\n";

        continue;
    }

    Step::create([
        'category_id' => $cat->id,
        'title' => $obsTitle,
        'estimated_minutes' => 5,
        'sort_order' => -1,
    ]);
    echo "  added to: {$cat->name}\n";
}

// --- Summary ---------------------------------------------------------------
$open = Step::where('status', '!=', 'done')->sum('estimated_minutes');
echo "\n=== Summary ===\n";
echo '  Open work remaining: '.round($open / 60, 1)."h\n";
echo '  Unplanned steps: '.Step::unplanned()->count()."\n";
echo "Done!\n";
