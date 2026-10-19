<?php

/**
 * Seed the YouTube Channel — Ores parent category with sub-categories and steps.
 *
 * Run: php _seed_youtube.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Step;

// ────────────────────────────────────────────────
// 1. Create parent category
// ────────────────────────────────────────────────
$parent = Category::create([
    'name' => 'YouTube Channel — Ores',
    'description' => 'Document the senior transition with real numbers — dev vlogs + career strategy',
    'color' => '#EF4444', // red (YouTube)
    'priority' => 'normal',
    'deadline' => '2027-03-01',
    'sort_order' => 5,
]);

echo "Created parent: {$parent->name} (id={$parent->id})\n";

// ────────────────────────────────────────────────
// 2. Sub-category: Setup & Equipment
// ────────────────────────────────────────────────
$setup = Category::create([
    'name' => 'YT — Setup & Equipment',
    'description' => 'Filming corner, software, chibi commission, channel creation',
    'color' => '#F97316', // orange
    'priority' => 'high',
    'deadline' => '2026-11-01',
    'sort_order' => 0,
    'parent_id' => $parent->id,
]);
echo "Created: {$setup->name}\n";

$setupSteps = [
    ['title' => 'Set up filming corner: desk, Sony ZV on tripod, DJI mic, desk lamp', 'estimated_minutes' => 60],
    ['title' => 'Install OBS for screen recording — test capture quality', 'estimated_minutes' => 30],
    ['title' => 'Install CapCut — learn auto-captions and basic cuts', 'estimated_minutes' => 45],
    ['title' => 'Install veadotube mini (PNGtuber tool) — test with mic input', 'estimated_minutes' => 30],
    ['title' => 'Record a 30-second test video — calibrate audio/video quality', 'estimated_minutes' => 20],
    ['title' => 'Delete test video and note what to adjust', 'estimated_minutes' => 5],
];
foreach ($setupSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $setup->id, 'sort_order' => $i]));
}

// ────────────────────────────────────────────────
// 3. Sub-category: Branding & Identity
// ────────────────────────────────────────────────
$branding = Category::create([
    'name' => 'YT — Branding & Identity',
    'description' => 'Chibi avatar, channel art, bio, handle, thumbnail template',
    'color' => '#A855F7', // purple
    'priority' => 'high',
    'deadline' => '2026-11-01',
    'sort_order' => 1,
    'parent_id' => $parent->id,
]);
echo "Created: {$branding->name}\n";

$brandingSteps = [
    ['title' => 'Commission chibi character on Fiverr — 6 expressions: neutral, happy, frustrated, excited, thinking, celebrating', 'estimated_minutes' => 30],
    ['title' => 'Review chibi drafts and request revisions', 'estimated_minutes' => 20],
    ['title' => 'Set up chibi poses in veadotube mini', 'estimated_minutes' => 30],
    ['title' => 'Create YouTube channel — set handle (@oresdev or @ores)', 'estimated_minutes' => 10],
    ['title' => 'Write channel bio: "Mid-level dev documenting the transition to senior — every skill, every decision, every paycheck."', 'estimated_minutes' => 15],
    ['title' => 'Upload chibi as profile picture', 'estimated_minutes' => 5],
    ['title' => 'Create channel banner (Canva) — name + tagline + chibi', 'estimated_minutes' => 30],
    ['title' => 'Design a basic thumbnail template (Canva) — reusable for first 10 videos', 'estimated_minutes' => 30],
];
foreach ($brandingSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $branding->id, 'sort_order' => $i]));
}

// ────────────────────────────────────────────────
// 4. Sub-category: First 5 Videos
// ────────────────────────────────────────────────
$first5 = Category::create([
    'name' => 'YT — First 5 Videos',
    'description' => 'Script, record, edit, and publish the first 5 videos (biweekly)',
    'color' => '#EF4444', // red
    'priority' => 'high',
    'deadline' => '2027-01-15',
    'sort_order' => 2,
    'parent_id' => $parent->id,
]);
echo "Created: {$first5->name}\n";

// Video 1: Origin Story
$v1 = Step::create([
    'title' => 'Video 1: Origin Story — "I make $1,800/month. Here\'s my plan to 3x that."',
    'category_id' => $first5->id,
    'sort_order' => 0,
    'estimated_minutes' => 300,
]);
$v1Children = [
    ['title' => 'V1: Write script/outline — who I am, 4 years in, salary, goals', 'estimated_minutes' => 45],
    ['title' => 'V1: Record — talking chibi + setup b-roll + tracker screen', 'estimated_minutes' => 60],
    ['title' => 'V1: Edit — cuts, captions, intro card', 'estimated_minutes' => 90],
    ['title' => 'V1: Create thumbnail + write title + description + tags', 'estimated_minutes' => 30],
    ['title' => 'V1: Publish and share on LinkedIn', 'estimated_minutes' => 15],
];
foreach ($v1Children as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $first5->id, 'parent_id' => $v1->id, 'sort_order' => $i]));
}

// Video 2: What Senior Means
$v2 = Step::create([
    'title' => 'Video 2: What Senior Actually Means — "Nobody defines what that means."',
    'category_id' => $first5->id,
    'sort_order' => 1,
    'estimated_minutes' => 300,
]);
$v2Children = [
    ['title' => 'V2: Write script — my definition, skill gaps, what I\'m closing', 'estimated_minutes' => 45],
    ['title' => 'V2: Record — chibi + screen (examples of senior vs mid-level decisions)', 'estimated_minutes' => 60],
    ['title' => 'V2: Edit — cuts, captions', 'estimated_minutes' => 90],
    ['title' => 'V2: Thumbnail + title + description + tags', 'estimated_minutes' => 30],
    ['title' => 'V2: Publish', 'estimated_minutes' => 15],
];
foreach ($v2Children as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $first5->id, 'parent_id' => $v2->id, 'sort_order' => $i]));
}

// Video 3: First Portfolio Project
$v3 = Step::create([
    'title' => 'Video 3: Shipping My First Portfolio Project — Inventory API walkthrough',
    'category_id' => $first5->id,
    'sort_order' => 2,
    'estimated_minutes' => 300,
]);
$v3Children = [
    ['title' => 'V3: Write script — why this project, architecture decisions, what I learned', 'estimated_minutes' => 45],
    ['title' => 'V3: Record — screen walkthrough of code + chibi commentary', 'estimated_minutes' => 60],
    ['title' => 'V3: Edit — code highlights, captions', 'estimated_minutes' => 90],
    ['title' => 'V3: Thumbnail + title + description + tags', 'estimated_minutes' => 30],
    ['title' => 'V3: Publish', 'estimated_minutes' => 15],
];
foreach ($v3Children as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $first5->id, 'parent_id' => $v3->id, 'sort_order' => $i]));
}

// Video 4: Productivity System
$v4 = Step::create([
    'title' => 'Video 4: My Developer Productivity System — the tracker app',
    'category_id' => $first5->id,
    'sort_order' => 3,
    'estimated_minutes' => 300,
]);
$v4Children = [
    ['title' => 'V4: Write script — show tracker, habits, capacity modes, focus timer', 'estimated_minutes' => 45],
    ['title' => 'V4: Record — screen demo of tracker app + chibi explaining philosophy', 'estimated_minutes' => 60],
    ['title' => 'V4: Edit — app demo highlights, captions', 'estimated_minutes' => 90],
    ['title' => 'V4: Thumbnail + title + description + tags', 'estimated_minutes' => 30],
    ['title' => 'V4: Publish', 'estimated_minutes' => 15],
];
foreach ($v4Children as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $first5->id, 'parent_id' => $v4->id, 'sort_order' => $i]));
}

// Video 5: AI Workflow
$v5 = Step::create([
    'title' => 'Video 5: How I Use AI to Code Faster — real workflow, honest take',
    'category_id' => $first5->id,
    'sort_order' => 4,
    'estimated_minutes' => 300,
]);
$v5Children = [
    ['title' => 'V5: Write script — when AI helps, when it fails, real examples from projects', 'estimated_minutes' => 45],
    ['title' => 'V5: Record — screen recording of actual AI-assisted coding session + chibi', 'estimated_minutes' => 60],
    ['title' => 'V5: Edit — side-by-side AI vs manual, captions', 'estimated_minutes' => 90],
    ['title' => 'V5: Thumbnail + title + description + tags', 'estimated_minutes' => 30],
    ['title' => 'V5: Publish', 'estimated_minutes' => 15],
];
foreach ($v5Children as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $first5->id, 'parent_id' => $v5->id, 'sort_order' => $i]));
}

// ────────────────────────────────────────────────
// 5. Sub-category: Content System
// ────────────────────────────────────────────────
$system = Category::create([
    'name' => 'YT — Content System',
    'description' => 'Repeatable workflow: script template, recording checklist, publishing process',
    'color' => '#0EA5E9', // sky blue
    'priority' => 'normal',
    'deadline' => '2026-12-01',
    'sort_order' => 3,
    'parent_id' => $parent->id,
]);
echo "Created: {$system->name}\n";

$systemSteps = [
    ['title' => 'Create a video script template (hook, body, CTA, outro)', 'estimated_minutes' => 30],
    ['title' => 'Create a recording checklist: mic check, lighting, OBS settings, chibi ready', 'estimated_minutes' => 15],
    ['title' => 'Create a publishing checklist: title, description, tags, thumbnail, end screen, cards', 'estimated_minutes' => 15],
    ['title' => 'Set up a content calendar — plan next 4 video topics at a time', 'estimated_minutes' => 20],
    ['title' => 'Learn YouTube SEO basics: titles, thumbnails, descriptions, tags', 'estimated_minutes' => 45],
    ['title' => 'Research 10 dev YouTubers — note what works in their format', 'estimated_minutes' => 60],
    ['title' => 'Create social media cross-posting routine (LinkedIn + Twitter/X per video)', 'estimated_minutes' => 15],
];
foreach ($systemSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $system->id, 'sort_order' => $i]));
}

// ────────────────────────────────────────────────
// 6. Sub-category: Growth & Evaluation
// ────────────────────────────────────────────────
$growth = Category::create([
    'name' => 'YT — Growth & Evaluation',
    'description' => '6-month evaluation, analytics review, community building',
    'color' => '#10B981', // emerald
    'priority' => 'low',
    'deadline' => '2027-03-01',
    'sort_order' => 4,
    'parent_id' => $parent->id,
]);
echo "Created: {$growth->name}\n";

$growthSteps = [
    ['title' => 'Set up YouTube Studio — understand analytics dashboard', 'estimated_minutes' => 20],
    ['title' => 'After video 5: review retention graphs — which parts do viewers skip?', 'estimated_minutes' => 30],
    ['title' => 'After video 5: identify best-performing video — plan a follow-up', 'estimated_minutes' => 20],
    ['title' => 'After video 10: evaluate cadence — ready for weekly?', 'estimated_minutes' => 15],
    ['title' => 'After video 10: review subscriber growth — is the positioning working?', 'estimated_minutes' => 20],
    ['title' => '6-month evaluation: subscribers, career impact, accountability effect, sustainability', 'estimated_minutes' => 45],
    ['title' => '6-month decision: continue / adjust format / pause', 'estimated_minutes' => 30],
    ['title' => 'If growing: commission Live2D chibi upgrade', 'estimated_minutes' => 30],
];
foreach ($growthSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $growth->id, 'sort_order' => $i]));
}

// ────────────────────────────────────────────────
// Summary
// ────────────────────────────────────────────────
$children = Category::where('parent_id', $parent->id)->get();
$totalSteps = 0;
echo "\n=== YouTube Channel — Ores — Summary ===\n";
foreach ($children as $child) {
    $count = $child->steps()->count();
    $totalSteps += $count;
    echo "  {$child->name}: {$count} steps\n";
}
echo "  TOTAL: {$totalSteps} steps across {$children->count()} sub-categories\n";
echo "\nDone!\n";
