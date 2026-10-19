<?php

/**
 * Seed the Relocation NL/DE parent category with sub-categories and steps.
 * Links existing categories (CV, Inventory API, Webhook, AI Platforms) as children.
 *
 * Run: php _seed_relocation.php
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
    'name' => 'Relocation NL/DE',
    'description' => 'Move abroad (Netherlands or Germany) for backend engineering role — target April 2027',
    'color' => '#059669', // emerald
    'priority' => 'critical',
    'deadline' => '2027-04-15',
    'sort_order' => 0,
]);

echo "Created parent: {$parent->name} (id={$parent->id})\n";

// ────────────────────────────────────────────────
// 2. Link existing categories as children
// ────────────────────────────────────────────────
$existingLinks = [
    1 => 'CV Improvement — Quick Wins',
    5 => 'CV — Final Polish',
    3 => 'Inventory REST API — Complete',
    4 => 'Webhook Delivery Service — Build',
    7 => 'AI Platforms — Get Hired',
];

foreach ($existingLinks as $id => $name) {
    $cat = Category::find($id);
    if ($cat) {
        $cat->update(['parent_id' => $parent->id]);
        echo "  Linked existing: {$cat->name} (id={$id})\n";
    } else {
        echo "  WARNING: Category id={$id} not found ({$name})\n";
    }
}

// ────────────────────────────────────────────────
// 3. Create new sub-categories with steps
// ────────────────────────────────────────────────

// --- LinkedIn Profile Setup ---
$linkedin = Category::create([
    'name' => 'LinkedIn — Profile Setup',
    'description' => 'Quick LinkedIn update for international visibility',
    'color' => '#0A66C2',
    'priority' => 'high',
    'deadline' => '2026-09-28',
    'sort_order' => 10,
    'parent_id' => $parent->id,
]);
echo "Created: {$linkedin->name}\n";

$linkedinSteps = [
    ['title' => 'Update headline: Backend Engineer | PHP/Laravel | API Design | Open to NL/DE relocation', 'estimated_minutes' => 5],
    ['title' => 'Add SQLI experience with concrete achievements', 'estimated_minutes' => 15],
    ['title' => 'Add Viveris experience with concrete achievements', 'estimated_minutes' => 15],
    ['title' => 'Add ENSIAS education', 'estimated_minutes' => 5],
    ['title' => 'Add skills: PHP, Laravel, REST APIs, SQL, PostgreSQL, Redis, Docker, Git', 'estimated_minutes' => 5],
    ['title' => 'Set "Open to Work" targeting Netherlands + Germany', 'estimated_minutes' => 2],
    ['title' => 'Add portfolio projects (Inventory API, Webhook Service) once done', 'estimated_minutes' => 10],
    ['title' => 'Write short About section focused on backend engineering', 'estimated_minutes' => 15],
];
foreach ($linkedinSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $linkedin->id, 'sort_order' => $i]));
}

// --- Relocation Platforms ---
$platforms = Category::create([
    'name' => 'Relocation Platforms — Get Visible',
    'description' => 'Create profiles on platforms that match devs with visa-sponsoring employers',
    'color' => '#7C3AED',
    'priority' => 'high',
    'deadline' => '2026-12-15',
    'sort_order' => 20,
    'parent_id' => $parent->id,
]);
echo "Created: {$platforms->name}\n";

$platformSteps = [
    ['title' => 'Research Honeypot.io — understand how it works, salary ranges, company profiles', 'estimated_minutes' => 30],
    ['title' => 'Create Honeypot profile — upload CV, link GitHub, write profile', 'estimated_minutes' => 45],
    ['title' => 'Research Relocate.me — how it differs from Honeypot', 'estimated_minutes' => 20],
    ['title' => 'Create Relocate.me profile', 'estimated_minutes' => 30],
    ['title' => 'Research Landing.jobs — check NL/DE listings', 'estimated_minutes' => 20],
    ['title' => 'Create Landing.jobs profile', 'estimated_minutes' => 30],
    ['title' => 'Set up job alerts on LinkedIn for PHP/Laravel NL/DE with visa sponsorship', 'estimated_minutes' => 10],
    ['title' => 'Research companies that sponsor in NL: Booking, Mollie, Sendcloud, Spryker, MessageBird', 'estimated_minutes' => 45],
    ['title' => 'Research companies that sponsor in DE: Trivago, CHECK24, Idealo, Personio, Jimdo', 'estimated_minutes' => 45],
    ['title' => 'Create a target company list with application links', 'estimated_minutes' => 30],
];
foreach ($platformSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $platforms->id, 'sort_order' => $i]));
}

// --- Interview Preparation ---
$interview = Category::create([
    'name' => 'Interview Preparation',
    'description' => 'Prepare for European backend engineering interviews: system design, coding, behavioral, take-homes',
    'color' => '#DC2626',
    'priority' => 'high',
    'deadline' => '2027-01-15',
    'sort_order' => 30,
    'parent_id' => $parent->id,
]);
echo "Created: {$interview->name}\n";

// System design sub-steps
$sysDesign = Step::create([
    'title' => 'System Design Preparation',
    'category_id' => $interview->id,
    'sort_order' => 0,
    'estimated_minutes' => 600,
]);
$sysDesignChildren = [
    ['title' => 'Study: Designing a REST API (pagination, filtering, versioning, auth)', 'estimated_minutes' => 60],
    ['title' => 'Study: Designing a webhook delivery system (queues, retries, backoff)', 'estimated_minutes' => 60],
    ['title' => 'Study: Caching strategies (Redis, cache invalidation, CDN)', 'estimated_minutes' => 60],
    ['title' => 'Study: Database scaling (indexing, sharding, read replicas)', 'estimated_minutes' => 60],
    ['title' => 'Study: Event-driven architecture (event sourcing, CQRS basics)', 'estimated_minutes' => 60],
    ['title' => 'Study: Authentication & authorization (OAuth, JWT, API keys)', 'estimated_minutes' => 45],
    ['title' => 'Practice: Whiteboard design a notification service', 'estimated_minutes' => 45],
    ['title' => 'Practice: Whiteboard design a URL shortener', 'estimated_minutes' => 45],
    ['title' => 'Practice: Whiteboard design an inventory management API', 'estimated_minutes' => 45],
    ['title' => 'Mock interview: system design on Pramp or Exponent (free)', 'estimated_minutes' => 60],
    ['title' => 'Mock interview: system design round 2', 'estimated_minutes' => 60],
];
foreach ($sysDesignChildren as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $interview->id, 'parent_id' => $sysDesign->id, 'sort_order' => $i]));
}

// Coding interview sub-steps
$coding = Step::create([
    'title' => 'Coding Interview Preparation',
    'category_id' => $interview->id,
    'sort_order' => 1,
    'estimated_minutes' => 300,
]);
$codingChildren = [
    ['title' => 'LeetCode Easy: Arrays & Strings (10 problems)', 'estimated_minutes' => 60],
    ['title' => 'LeetCode Easy: Hash Maps & Sets (10 problems)', 'estimated_minutes' => 60],
    ['title' => 'LeetCode Easy-Medium: Sorting & Searching (5 problems)', 'estimated_minutes' => 45],
    ['title' => 'LeetCode Medium: Trees & Graphs basics (5 problems)', 'estimated_minutes' => 60],
    ['title' => 'Practice: PHP-specific coding patterns (collections, generators)', 'estimated_minutes' => 30],
    ['title' => 'Practice: SQL interview questions (JOINs, window functions, optimization)', 'estimated_minutes' => 45],
];
foreach ($codingChildren as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $interview->id, 'parent_id' => $coding->id, 'sort_order' => $i]));
}

// Behavioral sub-steps
$behavioral = Step::create([
    'title' => 'Behavioral Interview Preparation',
    'category_id' => $interview->id,
    'sort_order' => 2,
    'estimated_minutes' => 180,
]);
$behavioralChildren = [
    ['title' => 'Write STAR story: Debugging a critical production issue at SQLI', 'estimated_minutes' => 20],
    ['title' => 'Write STAR story: Handling a tight deadline / scope change at Viveris', 'estimated_minutes' => 20],
    ['title' => 'Write STAR story: Taking technical ownership of a feature', 'estimated_minutes' => 20],
    ['title' => 'Write STAR story: Disagreeing with a technical decision', 'estimated_minutes' => 20],
    ['title' => 'Write STAR story: Learning a new technology quickly', 'estimated_minutes' => 20],
    ['title' => 'Practice: Tell me about yourself (2-min pitch in English)', 'estimated_minutes' => 30],
    ['title' => 'Practice: Why do you want to relocate? (honest, concise answer)', 'estimated_minutes' => 15],
    ['title' => 'Practice: Where do you see yourself in 3 years?', 'estimated_minutes' => 15],
];
foreach ($behavioralChildren as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $interview->id, 'parent_id' => $behavioral->id, 'sort_order' => $i]));
}

// Take-home sub-steps
$takehome = Step::create([
    'title' => 'Take-Home Project Preparation',
    'category_id' => $interview->id,
    'sort_order' => 3,
    'estimated_minutes' => 180,
]);
$takehomeChildren = [
    ['title' => 'Practice: Build a mini REST API in 3 hours (timed)', 'estimated_minutes' => 180],
    ['title' => 'Practice: Build a mini CRUD + tests in 2 hours (timed)', 'estimated_minutes' => 120],
    ['title' => 'Create a take-home template: Laravel + tests + Docker + README', 'estimated_minutes' => 60],
];
foreach ($takehomeChildren as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $interview->id, 'parent_id' => $takehome->id, 'sort_order' => $i]));
}

// --- Visa & Documents ---
$visa = Category::create([
    'name' => 'Visa & Documents',
    'description' => 'Prepare all documents needed for NL Kennismigrant or DE Blue Card visa',
    'color' => '#0891B2',
    'priority' => 'normal',
    'deadline' => '2027-03-01',
    'sort_order' => 40,
    'parent_id' => $parent->id,
]);
echo "Created: {$visa->name}\n";

$visaSteps = [
    ['title' => 'Research NL Kennismigrant visa requirements in detail', 'estimated_minutes' => 30],
    ['title' => 'Research DE Blue Card visa requirements in detail', 'estimated_minutes' => 30],
    ['title' => 'Get ENSIAS diploma apostilled (Ministry of Foreign Affairs)', 'estimated_minutes' => 120],
    ['title' => 'Get criminal record certificate (casier judiciaire)', 'estimated_minutes' => 60],
    ['title' => 'Get birth certificate copy (acte de naissance)', 'estimated_minutes' => 30],
    ['title' => 'Research credential evaluation: Nuffic (NL) / anabin (DE)', 'estimated_minutes' => 30],
    ['title' => 'Start Nuffic credential evaluation if targeting NL', 'estimated_minutes' => 60],
    ['title' => 'Ensure passport is valid for 6+ months after planned move date', 'estimated_minutes' => 15],
    ['title' => 'Prepare digital copies of all documents (scanned, PDF)', 'estimated_minutes' => 30],
    ['title' => 'Research health insurance requirements for NL/DE', 'estimated_minutes' => 20],
    ['title' => 'Research German embassy Rabat appointment process', 'estimated_minutes' => 15],
    ['title' => 'Gather proof of work experience (contracts, reference letters from SQLI/Viveris)', 'estimated_minutes' => 60],
];
foreach ($visaSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $visa->id, 'sort_order' => $i]));
}

// --- Financial Preparation ---
$finance = Category::create([
    'name' => 'Financial Preparation',
    'description' => 'Build savings runway and set up financial infrastructure for the move',
    'color' => '#16A34A',
    'priority' => 'normal',
    'deadline' => '2027-03-01',
    'sort_order' => 50,
    'parent_id' => $parent->id,
]);
echo "Created: {$finance->name}\n";

$financeSteps = [
    ['title' => 'Calculate total move cost: flights, first month rent + deposit, living expenses buffer', 'estimated_minutes' => 30],
    ['title' => 'Set monthly savings target to reach 50,000 MAD runway by March 2027', 'estimated_minutes' => 15],
    ['title' => 'Open Wise account for EUR transfers', 'estimated_minutes' => 20],
    ['title' => 'Open Revolut account as backup', 'estimated_minutes' => 20],
    ['title' => 'Research temporary housing options in Amsterdam/Berlin (first 1-2 months)', 'estimated_minutes' => 30],
    ['title' => 'Research relocation packages — what companies typically offer', 'estimated_minutes' => 20],
    ['title' => 'Research NL 30% ruling: eligibility, how to claim, impact on net salary', 'estimated_minutes' => 30],
    ['title' => 'Research tax implications: Moroccan tax on foreign income, double taxation treaties', 'estimated_minutes' => 30],
    ['title' => 'Plan notice period at current job (check contract terms)', 'estimated_minutes' => 15],
];
foreach ($financeSteps as $i => $s) {
    Step::create(array_merge($s, ['category_id' => $finance->id, 'sort_order' => $i]));
}

// ────────────────────────────────────────────────
// Summary
// ────────────────────────────────────────────────
$children = Category::where('parent_id', $parent->id)->get();
$totalSteps = 0;
echo "\n=== Relocation NL/DE — Summary ===\n";
foreach ($children as $child) {
    $count = $child->steps()->count();
    $totalSteps += $count;
    echo "  {$child->name}: {$count} steps\n";
}
echo "  TOTAL: {$totalSteps} steps across {$children->count()} sub-categories\n";
echo "\nDone!\n";
