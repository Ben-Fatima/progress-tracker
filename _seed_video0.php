<?php

/**
 * Seed Video 0 sub-category under YouTube Channel — Ores.
 *
 * Run: php _seed_video0.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Step;

// Find parent
$parent = Category::where('name', 'YouTube Channel — Ores')->firstOrFail();
echo "Parent: {$parent->name} (id={$parent->id})\n";

// Create Video 0 sub-category — sort_order -1 to appear before Setup
$video0 = Category::create([
    'name' => 'YT — Video 0: The Setup',
    'description' => 'Episode Zero: film the studio build process with phone, edit later as first upload or b-roll',
    'color' => '#F43F5E', // rose
    'priority' => 'high',
    'deadline' => '2026-11-15',
    'sort_order' => -1,
    'parent_id' => $parent->id,
]);
echo "Created: {$video0->name} (id={$video0->id})\n";

$steps = [
    // Pre-production
    ['title' => 'V0: Plan shots list — empty room, unboxing, assembly, cable management, first test, reveal', 'estimated_minutes' => 15],
    ['title' => 'V0: Charge phone, clear storage, set to 1080p/30fps minimum', 'estimated_minutes' => 5],

    // Filming (with phone during setup)
    ['title' => 'V0: Film the empty room / new house space — "before" shot', 'estimated_minutes' => 5],
    ['title' => 'V0: Film unboxing/placing Sony ZV + DJI mic + lamp on desk', 'estimated_minutes' => 15],
    ['title' => 'V0: Film cable management and desk organization', 'estimated_minutes' => 10],
    ['title' => 'V0: Film first test recording on Sony ZV — reaction to seeing yourself', 'estimated_minutes' => 10],
    ['title' => 'V0: Film lighting tests — compare with/without lamp', 'estimated_minutes' => 5],
    ['title' => 'V0: Film the finished setup reveal — clean desk, everything in place', 'estimated_minutes' => 5],
    ['title' => 'V0: Record voiceover intro: "I\'m building a YouTube studio to document my journey..."', 'estimated_minutes' => 15],

    // Post-production
    ['title' => 'V0: Import all clips to CapCut', 'estimated_minutes' => 10],
    ['title' => 'V0: Edit — assemble timeline, cut dead air, add captions', 'estimated_minutes' => 60],
    ['title' => 'V0: Add background music (royalty-free, lo-fi or ambient)', 'estimated_minutes' => 15],
    ['title' => 'V0: Add text overlays: equipment names + prices in MAD', 'estimated_minutes' => 20],
    ['title' => 'V0: Create thumbnail — split image: empty desk vs finished setup', 'estimated_minutes' => 30],
    ['title' => 'V0: Write title + description + tags', 'estimated_minutes' => 15],
    ['title' => 'V0: Publish as first video on channel', 'estimated_minutes' => 10],
];

foreach ($steps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $video0->id, 'sort_order' => $i]));
}

echo "\nSeeded {$video0->name}: " . count($steps) . " steps\n";
echo "Done!\n";
