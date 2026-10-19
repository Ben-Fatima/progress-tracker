<?php

/**
 * Replace "Portfolio Website — Rebuild" with the structure decided during grilling.
 *
 * Decisions encoded here:
 *  - Two sites: fatimazahra.dev = portfolio, cv.fatimazahra.dev = resume tool
 *  - Audience: remote-first international, English only
 *  - Fresh scaffold inside the existing Portfolio repo (keeps Vercel/domain/SSL)
 *  - MDX case studies + typed TS data, dark-only terminal aesthetic
 *  - 2 production case studies (employers named, clients never) + 2 project case studies
 *  - Cut order if time runs short: FileImporter first, then design polish
 *
 * Run: php _seed_portfolio_rebuild.php
 */

use App\Models\Category;
use App\Models\Step;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// --- Remove the superseded structure (root + sub-categories + their steps) ---
$old = Category::where('name', 'Portfolio Website — Rebuild')->first();

if ($old) {
    $ids = Category::where('parent_id', $old->id)->pluck('id')->push($old->id)->all();
    $deletedSteps = Step::whereIn('category_id', $ids)->delete();
    Category::whereIn('id', $ids)->delete();
    echo 'Removed old structure: '.count($ids)." categories, {$deletedSteps} steps\n\n";
}

// Top-level parent: the UI supports 2 levels (parent + children), so this must
// not be nested under Relocation or its own children become invisible.
$root = Category::create([
    'name' => 'Portfolio Website — Rebuild',
    'description' => 'Rebuild fatimazahra.dev as a reading-first portfolio for remote-first international roles. Fresh Next.js 16 scaffold inside the existing Portfolio repo. English only, dark-only terminal aesthetic, MDX case studies. Resume tool stays separate at cv.fatimazahra.dev. Budget: 2-3 weeks at ~10h/week.',
    'color' => '#4F46E5',
    'priority' => 'high',
    'deadline' => '2026-10-20',
    'sort_order' => 6,
    'parent_id' => null,
]);
echo "Created root: {$root->name} (id={$root->id})\n\n";

$subs = [
    [
        'name' => 'Portfolio — Foundation & Content Model',
        'description' => 'Fresh Next.js 16 scaffold inside the existing Portfolio repo to keep Vercel, domain and SSL wiring. MDX for case studies, typed TS data for everything else, so adding a case study later is a file drop.',
        'color' => '#6366F1',
        'priority' => 'critical',
        'steps' => [
            ['title' => 'Archive current Portfolio contents on a legacy branch before wiping main', 'estimated_minutes' => 20],
            ['title' => 'Scaffold Next.js 16 + App Router + TypeScript + Tailwind in the existing repo', 'estimated_minutes' => 45],
            ['title' => 'Copy tsconfig / eslint / prettier / tailwind config from the Resume app for shared conventions', 'estimated_minutes' => 30],
            ['title' => 'Set up MDX pipeline (content/work/*.mdx + dynamic /work/[slug] route)', 'estimated_minutes' => 60],
            ['title' => 'Define the typed data files: profile, experience, stack, links', 'estimated_minutes' => 45],
            ['title' => 'Define case study frontmatter schema (title, group, summary, stack, repo, order)', 'estimated_minutes' => 30],
            ['title' => 'Build the root layout, header and footer', 'estimated_minutes' => 60],
            ['title' => 'Delete legacy code: pages router, react-router-dom, api/hello, unused images, duplicate me.jpg', 'estimated_minutes' => 30],
            ['title' => 'Confirm Vercel still builds and deploys from the repo after the rewrite', 'estimated_minutes' => 30],
        ],
    ],
    [
        'name' => 'Portfolio — Design System (dark, terminal, reading-first)',
        'description' => 'Technical/terminal aesthetic, dark only for v1. Monospace for headings, labels and code ONLY - never body text, because the site has to carry long-form case studies.',
        'color' => '#0EA5E9',
        'priority' => 'high',
        'steps' => [
            ['title' => 'Define dark palette as Tailwind theme tokens (one accent, muted base, no ad-hoc utility colors)', 'estimated_minutes' => 45],
            ['title' => 'Pick font pair: monospace for headings/labels/code + legible prose font for body', 'estimated_minutes' => 45],
            ['title' => 'Define type scale, line length (~65-75ch) and vertical rhythm for long-form reading', 'estimated_minutes' => 45],
            ['title' => 'Build the case study card component with the group label', 'estimated_minutes' => 45],
            ['title' => 'Build the case study detail layout (headings, code blocks, callouts, diagrams)', 'estimated_minutes' => 60],
            ['title' => 'Build the hero: name, positioning line, Download CV CTA, social links', 'estimated_minutes' => 60],
            ['title' => 'Style MDX elements (h2/h3, pre, code, blockquote, lists, tables)', 'estimated_minutes' => 60],
            ['title' => 'Verify dark-mode contrast passes WCAG AA on body and accent text', 'estimated_minutes' => 30],
            ['title' => 'Mobile pass on every breakpoint, real device not just devtools', 'estimated_minutes' => 60],
        ],
    ],
    [
        'name' => 'Portfolio — Production Work Case Studies',
        'description' => 'Two concrete case studies from professional work. Anonymization rules: employers may be named, clients NEVER, no employer code, relative metrics only. Viveris gets one short paragraph on problem type only - no architecture detail on a system currently accessible.',
        'color' => '#EC4899',
        'priority' => 'critical',
        'steps' => [
            ['title' => 'Write the reusable case study template (Problem → Constraints → Design → Tradeoffs → Result → What I would change)', 'estimated_minutes' => 45],
            ['title' => 'Magento import: write Problem & Constraints (memory ceiling, admin import volume)', 'estimated_minutes' => 45],
            ['title' => 'Magento import: write the Design section (full in-memory load → batch processing)', 'estimated_minutes' => 60],
            ['title' => 'Magento import: write Tradeoffs (batch size, transaction boundaries, error recovery)', 'estimated_minutes' => 45],
            ['title' => 'Magento import: state the Result in relative terms only (no client infra figures)', 'estimated_minutes' => 30],
            ['title' => 'Magento import: add a before/after flow diagram', 'estimated_minutes' => 60],
            ['title' => 'Production incident: pick one incident that is safe to describe and outline it', 'estimated_minutes' => 30],
            ['title' => 'Production incident: write diagnosis narrative (symptom → hypotheses → evidence → root cause)', 'estimated_minutes' => 60],
            ['title' => 'Production incident: write the fix, the verification and the prevention follow-up', 'estimated_minutes' => 45],
            ['title' => 'Anonymization review: no client names, no employer code, no undefendable metrics', 'estimated_minutes' => 30],
            ['title' => 'Write the single short Viveris paragraph (problem type only)', 'estimated_minutes' => 20],
        ],
    ],
    [
        'name' => 'Portfolio — Engineering Project Case Studies',
        'description' => 'Own projects, full depth, real repos, code and tradeoffs shown freely since there is no anonymization constraint. Resume tool appears as a card, not a case study. FileImporter is the pre-committed first cut if time runs short.',
        'color' => '#10B981',
        'priority' => 'high',
        'steps' => [
            ['title' => 'Stock Tracker: write Problem & Constraints', 'estimated_minutes' => 45],
            ['title' => 'Stock Tracker: write the event-sourcing design decision and why not mutable stock', 'estimated_minutes' => 60],
            ['title' => 'Stock Tracker: write Tradeoffs (derived-state cost, transactional integrity, query complexity)', 'estimated_minutes' => 45],
            ['title' => 'Stock Tracker: write an honest "What I would change" section', 'estimated_minutes' => 30],
            ['title' => 'Stock Tracker: add architecture diagram', 'estimated_minutes' => 60],
            ['title' => 'FileImporter: write Problem & Constraints (500MB+ files, memory limits)', 'estimated_minutes' => 45],
            ['title' => 'FileImporter: write the streaming vs in-memory decision with real benchmarks', 'estimated_minutes' => 60],
            ['title' => 'FileImporter: write Tradeoffs and "What I would change"', 'estimated_minutes' => 45],
            ['title' => 'FileImporter: add flow diagram', 'estimated_minutes' => 45],
            ['title' => 'Resume tool: write the card copy (data-driven rendering, dual-locale, headless-Chromium PDF)', 'estimated_minutes' => 30],
        ],
    ],
    [
        'name' => 'Portfolio — Launch & Integration',
        'description' => 'Ship it, wire it into every application surface, then get brutal feedback. Contact is email + LinkedIn + GitHub only, no form. Download CV is the primary CTA.',
        'color' => '#DC2626',
        'priority' => 'high',
        'steps' => [
            ['title' => 'Export a current CV PDF from the Resume app and wire it as the primary CTA', 'estimated_minutes' => 30],
            ['title' => 'Add email, LinkedIn and GitHub links in header and footer (no contact form)', 'estimated_minutes' => 30],
            ['title' => 'Write the About page as an engineering narrative', 'estimated_minutes' => 60],
            ['title' => 'Add per-page metadata, Open Graph tags and an OG image (old site had no title tag at all)', 'estimated_minutes' => 45],
            ['title' => 'Add sitemap.xml, robots.txt and JSON-LD Person data', 'estimated_minutes' => 30],
            ['title' => 'Run Lighthouse: target 95+ performance, 100 accessibility', 'estimated_minutes' => 60],
            ['title' => 'Verify every outbound link returns 200', 'estimated_minutes' => 30],
            ['title' => 'Point cv.fatimazahra.dev at the Resume app and confirm SSL', 'estimated_minutes' => 30],
            ['title' => 'Deploy to production and verify the apex domain + SSL', 'estimated_minutes' => 30],
            ['title' => 'Proofread every sentence on the site', 'estimated_minutes' => 45],
            ['title' => 'Update LinkedIn featured section and GitHub profile README', 'estimated_minutes' => 30],
            ['title' => 'Get brutal feedback from 2 senior engineers, log it, apply it', 'estimated_minutes' => 60],
        ],
    ],
];

$totalSteps = 0;

foreach ($subs as $i => $sub) {
    $steps = $sub['steps'];
    unset($sub['steps']);

    $cat = Category::create($sub + [
        'sort_order' => $i,
        'parent_id' => $root->id,
    ]);

    foreach ($steps as $order => $step) {
        Step::create($step + [
            'category_id' => $cat->id,
            'sort_order' => $order,
        ]);
    }

    $totalSteps += count($steps);
    $minutes = array_sum(array_column($steps, 'estimated_minutes'));
    echo '  '.str_pad((string) count($steps), 3, ' ', STR_PAD_LEFT).' steps  '
        .str_pad(round($minutes / 60, 1).'h', 7, ' ', STR_PAD_LEFT)."  |  {$cat->name} (id={$cat->id})\n";
}

echo "\n=== Summary ===\n";
echo '  Sub-categories: '.count($subs)."\n";
echo "  Total steps: {$totalSteps}\n";
echo '  Total estimate: '.round(array_sum(array_map(
    fn ($s) => array_sum(array_column($s['steps'], 'estimated_minutes')),
    $subs
)) / 60, 1)."h\n";
echo "Done!\n";
