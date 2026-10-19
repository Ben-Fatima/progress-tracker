<?php

/**
 * Add room setup steps to the YT — Setup & Equipment sub-category.
 *
 * Run: php _seed_room_setup.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Step;

// Find existing Setup & Equipment sub-category
$setup = Category::where('name', 'YT — Setup & Equipment')->firstOrFail();
$existingCount = $setup->steps()->count();
echo "Found: {$setup->name} (id={$setup->id}, existing steps={$existingCount})\n";

// Room setup steps — add after existing steps
$roomSteps = [
    // Furniture & layout
    ['title' => 'Move desk to Wall A, position KALLAX vertical next to desk (right side)', 'estimated_minutes' => 30],
    ['title' => 'Place bed on opposite wall (Wall C) — verify hidden from all camera angles', 'estimated_minutes' => 15],

    // KALLAX setup
    ['title' => 'Install KALLAX drawer inserts in bottom 2 cubes', 'estimated_minutes' => 15],
    ['title' => 'Arrange KALLAX Row 4 (top): Luffy + Zoro left, Saitama + Kakashi right', 'estimated_minutes' => 10],
    ['title' => 'Arrange KALLAX Row 3 (eye level): chess board left, books right', 'estimated_minutes' => 10],
    ['title' => 'Arrange KALLAX Row 2 (desk height): books + Luffy left, empty right (future)', 'estimated_minutes' => 5],

    // Wall decoration
    ['title' => 'Buy black frame for One Piece wanted poster (IKEA/Bricoma, ~50-100 MAD)', 'estimated_minutes' => 30],
    ['title' => 'Frame and hang OP poster above KALLAX', 'estimated_minutes' => 15],

    // Pegboard
    ['title' => 'Buy pegboard 40x60cm black + hooks (IKEA Skadis/Bricoma, ~150-250 MAD)', 'estimated_minutes' => 30],
    ['title' => 'Mount pegboard above monitor — centered on desk area', 'estimated_minutes' => 20],
    ['title' => 'Attach headphone hook + one small decorative item to pegboard', 'estimated_minutes' => 5],

    // Lighting
    ['title' => 'Buy monitor light bar (BenQ clone, Jumia/AliExpress, ~150-300 MAD)', 'estimated_minutes' => 15],
    ['title' => 'Install monitor light bar on top of external monitor', 'estimated_minutes' => 5],
    ['title' => 'Buy warm white USB LED strip (Jumia, ~30-50 MAD)', 'estimated_minutes' => 10],
    ['title' => 'Attach warm white LED strip behind desk (backlight behind monitor)', 'estimated_minutes' => 15],
    ['title' => 'Buy RGB USB LED strip (Jumia, ~50-100 MAD)', 'estimated_minutes' => 10],
    ['title' => 'Install RGB LED strip inside/behind KALLAX shelves', 'estimated_minutes' => 20],

    // Cable management
    ['title' => 'Buy cable clips + velcro ties (Jumia/Bricoma, ~30-50 MAD)', 'estimated_minutes' => 10],
    ['title' => 'Route and bundle all cables along desk legs and wall edges', 'estimated_minutes' => 30],

    // Future items (track as tasks)
    ['title' => 'Buy Lego Botanical set (Succulents/Bonsai) for pegboard/KALLAX (~300-500 MAD)', 'estimated_minutes' => 15],
];

foreach ($roomSteps as $i => $s) {
    Step::create(array_merge($s, [
        'category_id' => $setup->id,
        'sort_order' => $existingCount + $i,
    ]));
}

echo "Added " . count($roomSteps) . " room setup steps\n";
echo "Total steps in {$setup->name}: " . $setup->steps()->count() . "\n";
echo "Done!\n";
