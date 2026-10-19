<?php

use App\Livewire\CategoriesPage;
use App\Livewire\CategoryDetail;
use App\Livewire\DailyLogPage;
use App\Livewire\Dashboard;
use App\Livewire\DocumentDetail;
use App\Livewire\DocumentsPage;
use App\Livewire\ExportData;
use App\Livewire\FocusTimer;
use App\Livewire\HabitTracker;
use App\Livewire\PlanningPage;
use App\Livewire\PlanTomorrow;
use App\Livewire\SavingsTracker;
use App\Livewire\Search;
use App\Livewire\Templates;
use App\Livewire\WeeklyReview;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/categories', CategoriesPage::class)->name('categories');
Route::get('/categories/{category}', CategoryDetail::class)->name('categories.show');
Route::get('/focus', FocusTimer::class)->name('focus');
Route::get('/log', DailyLogPage::class)->name('log');
Route::get('/review', WeeklyReview::class)->name('review');
Route::get('/search', Search::class)->name('search');
Route::get('/export', ExportData::class)->name('export');
Route::get('/templates', Templates::class)->name('templates');
Route::get('/savings', SavingsTracker::class)->name('savings');
Route::get('/habits', HabitTracker::class)->name('habits');
Route::get('/plan-tomorrow', PlanTomorrow::class)->name('plan-tomorrow');
Route::get('/planning', PlanningPage::class)->name('planning');
Route::get('/documents', DocumentsPage::class)->name('documents');
Route::get('/documents/{document}', DocumentDetail::class)->name('documents.show');
