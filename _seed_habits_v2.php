<?php

/**
 * Add new habits aligned with relocation, career, health, and deen goals.
 * Run: php _seed_habits_v2.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Habit;

$existing = Habit::pluck('name')->toArray();
$nextOrder = (Habit::max('sort_order') ?? 0) + 1;

$habits = [
    // ── Deen ──
    ['name' => 'Fajr on time', 'icon' => "\xF0\x9F\x8C\x99", 'frequency' => 'daily'],       // crescent moon
    ['name' => 'Dhuhr on time', 'icon' => "\xE2\x98\x80\xEF\xB8\x8F", 'frequency' => 'daily'], // sun
    ['name' => 'Asr on time', 'icon' => "\xF0\x9F\x8C\xA4\xEF\xB8\x8F", 'frequency' => 'daily'], // sun behind cloud
    ['name' => 'Maghrib on time', 'icon' => "\xF0\x9F\x8C\x87", 'frequency' => 'daily'],     // sunset
    ['name' => 'Isha on time', 'icon' => "\xF0\x9F\x8C\x83", 'frequency' => 'daily'],        // night stars
    ['name' => 'Quran (10min)', 'icon' => "\xF0\x9F\x93\x96", 'frequency' => 'daily'],       // open book
    ['name' => 'Morning adhkar', 'icon' => "\xF0\x9F\xA4\xB2", 'frequency' => 'daily'],      // palms up
    ['name' => 'Evening adhkar', 'icon' => "\xF0\x9F\xA4\xB2", 'frequency' => 'daily'],      // palms up

    // ── Career / Relocation ──
    ['name' => 'Practice English (15min)', 'icon' => "\xF0\x9F\x87\xAC\xF0\x9F\x87\xA7", 'frequency' => 'daily'], // GB flag
    ['name' => 'Read tech article/docs (15min)', 'icon' => "\xF0\x9F\x93\x9A", 'frequency' => 'daily'], // books
    ['name' => 'Review 1 system design concept', 'icon' => "\xF0\x9F\x97\xBA\xEF\xB8\x8F", 'frequency' => 'daily'], // world map
    ['name' => 'GitHub commit', 'icon' => "\xF0\x9F\x92\xBB", 'frequency' => 'daily'],       // laptop

    // ── Health & Energy ──
    ['name' => 'Drink 2L water', 'icon' => "\xF0\x9F\x92\xA7", 'frequency' => 'daily'],      // droplet
    ['name' => 'Exercise/Walk (30min)', 'icon' => "\xF0\x9F\x8F\x83", 'frequency' => 'daily'], // runner
    ['name' => 'Morning skincare', 'icon' => "\xF0\x9F\xA7\xB4", 'frequency' => 'daily'],    // lotion
    ['name' => 'Night skincare', 'icon' => "\xE2\x9C\xA8", 'frequency' => 'daily'],          // sparkles
    ['name' => 'Sunscreen applied', 'icon' => "\xE2\x98\x80\xEF\xB8\x8F", 'frequency' => 'daily'], // sun
    ['name' => 'No phone 30min before bed', 'icon' => "\xF0\x9F\x93\xB5", 'frequency' => 'daily'], // no phone

    // ── Productivity & Mindset ──
    ['name' => 'Plan tomorrow (5min)', 'icon' => "\xF0\x9F\x93\x8B", 'frequency' => 'daily'], // clipboard
    ['name' => 'No social media during work', 'icon' => "\xF0\x9F\x9A\xAB", 'frequency' => 'daily'], // prohibited
    ['name' => 'Gratitude (3 things)', 'icon' => "\xF0\x9F\x99\x8F", 'frequency' => 'daily'], // folded hands

    // ── Financial ──
    ['name' => 'Track expenses', 'icon' => "\xF0\x9F\x92\xB0", 'frequency' => 'daily'],      // money bag
];

$added = 0;
foreach ($habits as $h) {
    if (in_array($h['name'], $existing)) {
        echo "  SKIP (exists): {$h['name']}\n";
        continue;
    }
    Habit::create([
        'name' => $h['name'],
        'icon' => $h['icon'],
        'frequency' => $h['frequency'],
        'sort_order' => $nextOrder++,
        'active' => true,
    ]);
    echo "  ADDED: {$h['name']}\n";
    $added++;
}

echo "\n=== Summary ===\n";
echo "Added: {$added} new habits\n";
echo "Total: " . Habit::count() . " habits\n";

// List all
echo "\nAll habits:\n";
foreach (Habit::orderBy('sort_order')->get() as $h) {
    echo "  [{$h->id}] {$h->name}\n";
}
