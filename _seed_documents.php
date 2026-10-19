<?php

/**
 * Seed documents linked to categories.
 *
 * Run: php _seed_documents.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Document;

$basePath = 'C:\Users\fz.benhammou\Desktop\Projects\Perso\Plan';

// Helper to find category by name
$catId = fn (string $name) => Category::where('name', $name)->first()?->id;

$documents = [
    // YouTube Channel — Ores
    [
        'title' => 'YouTube Channel — Ores Roadmap',
        'description' => 'Complete channel strategy: positioning, content plan, first 5 videos, production workflow, evaluation milestones',
        'file_path' => $basePath . '\youtube-channel-ores.md',
        'type' => 'plan',
        'category_id' => $catId('YouTube Channel — Ores'),
    ],
    [
        'title' => 'Room Setup — Dev Studio Plan',
        'description' => 'Room layout, KALLAX configuration, lighting plan, shopping list, cable management',
        'file_path' => $basePath . '\room-setup-studio.md',
        'type' => 'plan',
        'category_id' => $catId('YT — Setup & Equipment'),
    ],

    // Relocation
    [
        'title' => 'AI Platforms Prep Schedule',
        'description' => 'Schedule and prep for AI coding platform applications (Micro1, etc.)',
        'file_path' => $basePath . '\ai-platforms-prep-schedule.md',
        'type' => 'plan',
        'category_id' => $catId('AI Platforms — Get Hired'),
    ],
    [
        'title' => 'Micro1 — Coding Exercises',
        'description' => 'Practice exercises for Micro1 coding assessment',
        'file_path' => $basePath . '\micro1-coding-exercises.md',
        'type' => 'reference',
        'category_id' => $catId('AI Platforms — Get Hired'),
    ],
    [
        'title' => 'Micro1 — Deep Dive',
        'description' => 'Detailed analysis of Micro1 platform and requirements',
        'file_path' => $basePath . '\micro1-deep-dive.md',
        'type' => 'reference',
        'category_id' => $catId('AI Platforms — Get Hired'),
    ],
    [
        'title' => 'Micro1 — Interview Prep',
        'description' => 'Interview preparation notes for Micro1',
        'file_path' => $basePath . '\micro1-interview-prep.md',
        'type' => 'reference',
        'category_id' => $catId('Interview Preparation'),
    ],

    // CV
    [
        'title' => 'CV Improvement Plan',
        'description' => 'Detailed plan for CV improvements, experience descriptions, skills organization',
        'file_path' => 'C:\Users\fz.benhammou\Desktop\Projects\Perso\CV-IMPROVEMENT-PLAN.md',
        'type' => 'plan',
        'category_id' => $catId('CV — Final Polish'),
    ],

    // Progress Tracker
    [
        'title' => 'Progress Tracker — Conception',
        'description' => 'Original conception document for the progress tracker app',
        'file_path' => $basePath . '\Idea3-ProgressTracker\conception.md',
        'type' => 'reference',
        'category_id' => null,
    ],
    [
        'title' => 'Progress Tracker — Tasks',
        'description' => 'Task breakdown for progress tracker development',
        'file_path' => $basePath . '\Idea3-ProgressTracker\tasks.md',
        'type' => 'checklist',
        'category_id' => null,
    ],

    // Fikrify / Content Marketplace
    [
        'title' => 'Fikrify — Conception',
        'description' => 'Product conception and architecture for Fikrify content marketplace',
        'file_path' => $basePath . '\Idea1-ContentMarketplace\conception.md',
        'type' => 'reference',
        'category_id' => $catId('Fikrify — Finish & Deploy'),
    ],
    [
        'title' => 'Fikrify — Marketing Plan',
        'description' => 'Marketing strategy and 30-day launch plan',
        'file_path' => $basePath . '\Idea1-ContentMarketplace\marketing-plan.md',
        'type' => 'plan',
        'category_id' => $catId('Fikrify — Finish & Deploy'),
    ],
    [
        'title' => 'Fikrify — Product Roadmap (Post-MVP)',
        'description' => 'Feature roadmap after MVP launch',
        'file_path' => $basePath . '\Idea1-ContentMarketplace\product-roadmap-post-mvp.md',
        'type' => 'plan',
        'category_id' => $catId('Fikrify — Finish & Deploy'),
    ],
    [
        'title' => 'Fikrify — Remaining Tasks & Bugs',
        'description' => 'Outstanding bugs and tasks to fix before deploy',
        'file_path' => $basePath . '\Idea1-ContentMarketplace\bugs-to-fix.md',
        'type' => 'checklist',
        'category_id' => $catId('Fikrify — Finish & Deploy'),
    ],

    // Hana Atelier
    [
        'title' => 'Hana Matcha — Launch Plan',
        'description' => 'Launch strategy for Hana Matcha product line',
        'file_path' => $basePath . '\Idea2-HanaMatcha\hana-matcha-launch-plan.md',
        'type' => 'plan',
        'category_id' => $catId('Hana Atelier'),
    ],

    // Financial
    [
        'title' => '200K Car Plan',
        'description' => 'Financial planning for 200K MAD car purchase',
        'file_path' => $basePath . '\200k-car-plan.md',
        'type' => 'plan',
        'category_id' => $catId('Financial Preparation'),
    ],

    // General planning
    [
        'title' => '10-Week Planner',
        'description' => 'High-level 10-week planning overview',
        'file_path' => $basePath . '\10-week-planner-notion.md',
        'type' => 'plan',
        'category_id' => null,
    ],
];

$count = 0;
foreach ($documents as $doc) {
    Document::create($doc);
    $count++;
    $catName = $doc['category_id']
        ? Category::find($doc['category_id'])?->name ?? 'unknown'
        : 'uncategorized';
    echo "  Added: {$doc['title']} -> {$catName}\n";
}

echo "\nSeeded {$count} documents.\n";
echo "Done!\n";
