<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Step;
use Illuminate\Database\Seeder;

class CvImprovementSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'CV Improvement — Quick Wins',
                'description' => 'Rewrite resume content for maximum recruiter impact. No coding required.',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'steps' => [
                    [
                        'title' => 'Rewrite SQLI Morocco with metrics',
                        'children' => [
                            'Add number of client projects',
                            'Add query optimization results',
                            'Add module count or scope',
                            'Add incident resolution count',
                            'Add team collaboration details',
                        ],
                    ],
                    ['title' => 'Rewrite Viveris Technologies with early wins'],
                    ['title' => 'Remove or minimize Batha Auto internship'],
                    ['title' => 'Remove all numeric skill percentage levels'],
                    ['title' => 'Remove Angular from skills'],
                    ['title' => 'Remove React unless defensible in interview'],
                    ['title' => 'Reorganize skills into Core / Familiar / Tools'],
                    ['title' => 'Rewrite summary with specifics'],
                    ['title' => 'Remove Baccalaureate from education'],
                    ['title' => 'Remove WIP / alpha stage labels from projects'],
                ],
            ],
            [
                'name' => 'Fikrify — Finish & Deploy',
                'description' => 'Polish and verify the live Fikrify app before adding to CV.',
                'color' => '#8B5CF6',
                'sort_order' => 2,
                'steps' => [
                    ['title' => 'Verify fikrify.io is live and working'],
                    ['title' => 'Fix any broken features or UI bugs'],
                    ['title' => 'Check mobile responsiveness'],
                    ['title' => 'Ensure feedback route works end-to-end'],
                    ['title' => 'Ensure expand route works end-to-end'],
                    ['title' => 'Polish error handling'],
                    ['title' => 'Verify SEO / meta tags'],
                    ['title' => 'Verify Vercel deployment is stable'],
                    ['title' => 'Test rate limiting in production'],
                    ['title' => 'Verify share URLs work'],
                    ['title' => 'Check domain SSL'],
                    ['title' => 'Test OpenAI API key'],
                    ['title' => 'Add Fikrify to CV projects list'],
                ],
            ],
            [
                'name' => 'Inventory REST API — Complete',
                'description' => 'Finish tests and README for the event-sourced inventory system.',
                'color' => '#10B981',
                'sort_order' => 3,
                'steps' => [
                    ['title' => 'Implement Movement tests (Pest)'],
                    ['title' => 'Implement Stock tests (Pest)'],
                    ['title' => 'Replace default README with project README'],
                    ['title' => 'Optional: Basic CLI client'],
                ],
            ],
            [
                'name' => 'Webhook Delivery Service — Build',
                'description' => 'New project covering auth, queues, caching, and reliability patterns.',
                'color' => '#F59E0B',
                'sort_order' => 4,
                'steps' => [
                    ['title' => 'Set up Laravel project + Docker Compose'],
                    ['title' => 'JWT authentication (register/login/refresh/logout)'],
                    ['title' => 'API key authentication'],
                    ['title' => 'RBAC (admin/user roles)'],
                    ['title' => 'Channels CRUD'],
                    ['title' => 'Subscriptions CRUD'],
                    ['title' => 'Event publishing endpoint'],
                    ['title' => 'Queue-based webhook delivery'],
                    ['title' => 'Retry with exponential backoff'],
                    ['title' => 'Dead letter tracking'],
                    ['title' => 'Redis caching (cache-aside)'],
                    ['title' => 'Cache invalidation on update/delete'],
                    ['title' => 'Rate limiting per API key'],
                    ['title' => 'Delivery logs & history endpoint'],
                    ['title' => 'Unit tests (queue job logic)'],
                    ['title' => 'Feature tests (auth flows)'],
                    ['title' => 'Feature tests (RBAC)'],
                    ['title' => 'Feature tests (webhook delivery)'],
                    ['title' => 'README with architecture diagram'],
                    ['title' => 'API documentation'],
                ],
            ],
            [
                'name' => 'CV — Final Polish',
                'description' => 'Final pass on resume after all projects are complete.',
                'color' => '#EF4444',
                'sort_order' => 5,
                'steps' => [
                    ['title' => 'Reorder projects list'],
                    ['title' => 'Reframe all project descriptions for backend focus'],
                    ['title' => 'Remove Portfolio Website from projects'],
                    ['title' => 'Remove Backend Engineering Lab from projects'],
                    ['title' => 'Proofread all descriptions'],
                    ['title' => 'Verify all links are live'],
                ],
            ],

        ];

        foreach ($categories as $catData) {
            $steps = $catData['steps'];
            unset($catData['steps']);

            $category = Category::create($catData);

            foreach ($steps as $order => $stepData) {
                if (is_string($stepData)) {
                    $stepData = ['title' => $stepData];
                }

                $children = $stepData['children'] ?? [];
                unset($stepData['children']);

                $parent = Step::create([
                    'category_id' => $category->id,
                    'title' => $stepData['title'],
                    'sort_order' => $order,
                ]);

                foreach ($children as $childOrder => $childTitle) {
                    Step::create([
                        'category_id' => $category->id,
                        'parent_id' => $parent->id,
                        'title' => $childTitle,
                        'sort_order' => $childOrder,
                    ]);
                }
            }
        }
    }
}
