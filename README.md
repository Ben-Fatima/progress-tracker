# Progress Tracker

A personal productivity dashboard to track multi-project progress, focus sessions, and daily reflection. Built with Laravel, Livewire, and Tailwind CSS.

## Stack

- **Backend:** Laravel 13, PHP 8.3
- **Frontend:** Livewire 4, Tailwind CSS 4, Alpine.js
- **Database:** SQLite
- **Testing:** Pest

## Requirements

- PHP 8.2+ with extensions: pdo_sqlite, mbstring, openssl, tokenizer, xml, ctype, curl, fileinfo
- Composer
- Node.js 18+
- npm

## Getting Started

```bash
# 1. Clone and install dependencies
git clone <repo-url> progress-tracker
cd progress-tracker
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Create the database and seed initial data
php artisan migrate
php artisan db:seed

# 4. Start the development servers (two terminals)
php artisan serve          # http://localhost:8000 // /c/wamp64/bin/php/php8.3.28/php.exe artisan serve
npm run dev                # Vite asset server
```

Open http://localhost:8000 in your browser.

> **Note:** If your system defaults to an older PHP version, point to PHP 8.3 explicitly:
> ```bash
> C:\wamp64\bin\php\php8.3.28\php.exe artisan serve
> ```

## Seed Data

Running `php artisan db:seed` pre-populates the app with 5 categories from a CV improvement plan:

| Category | Steps | Description |
|----------|-------|-------------|
| CV Improvement — Quick Wins | 15 | Rewrite resume content for recruiter impact |
| Fikrify — Finish & Deploy | 13 | Polish and verify the live Fikrify app |
| Inventory REST API — Complete | 4 | Finish tests and README |
| Webhook Delivery Service — Build | 20 | New project: auth, queues, caching |
| CV — Final Polish | 6 | Final pass on resume after projects are done |

To reset and re-seed:

```bash
php artisan migrate:fresh --seed
```

---

## User Guide

### Navigation

The sidebar on the left provides access to all pages:

- **Search** — Global search across steps, categories, and logs (shortcut: `/`)
- **Dashboard** — Overview of streaks, stats, deadlines, and categories
- **Focus Timer** — Free and Pomodoro timer for tracking work sessions
- **Daily Log** — End-of-day check-in with ratings and reflection
- **Weekly Review** — Week-over-week stats, insights, and goal setting
- **Data & Export** — JSON backup, Markdown export, and step templates
- **Dark Mode** — Toggle at the bottom of the sidebar (remembers your preference)

---

### Dashboard

The dashboard is your home screen. It shows:

- **Current Streak** — Consecutive active days (with "yesterday grace" so it doesn't break before your morning starts). Best streak shown below.
- **Focus Today** — Total minutes spent in focus sessions today.
- **Steps Done Today** — Number of steps completed today.
- **Overdue** — Count of steps past their due date (highlighted red when > 0).
- **Continue Working On** — Suggests your next step: the first in-progress step, or the first todo step in your highest-priority category.
- **Upcoming Deadlines** — Shows overdue steps (red) and due-soon steps (amber) across all categories.
- **Categories** — All your project categories as cards with progress bars. Click a card to view its steps.
- **Recent Activity** — Timeline of recent actions: steps completed, focus sessions finished, daily logs saved.

From the dashboard you can:
- **Create a category** — Click "+ New Category", fill in name, description, and pick a color.
- **Edit a category** — Click the pencil icon on any card.
- **Delete a category** — Click the trash icon (deletes all steps inside it too).

---

### Category Detail

Click a category card to see its steps. This page shows:

- **Progress bar** — Visual indicator of how many steps are done.
- **Filter tabs** — Switch between All, Todo, and Done views.
- **Step list** — Each step has a checkbox, title, due date badge (if set), and action buttons.

Working with steps:

- **Complete a step** — Click the checkbox. It toggles between todo and done.
- **Start a step** — Click "Start" to mark it as in-progress (blue indicator).
- **Add a step** — Use the input at the bottom of the list.
- **Add a sub-step** — Click the "+" icon on any step to nest a sub-step under it.
- **Edit a step** — Click the pencil icon to rename, add notes, or set a due date.
- **Delete a step** — Click the trash icon (confirms before deleting).
- **View notes** — Click a step title to expand an inline notes panel. Add context, links, or blockers.
- **Set a due date** — When editing a step, use the date picker. Due dates appear as badges:
  - Red border + "Overdue" badge for past-due incomplete steps
  - Amber border for steps due within 2 days
  - Gray badge for future dates
  - Completed steps are never shown as overdue

Auto-complete rules:
- Completing all sub-steps of a parent automatically completes the parent.
- Unchecking a sub-step reverts the parent back to todo.
- Completing a parent step marks all its children as done.

---

### Focus Timer

Track time spent working with two modes:

#### Free Mode

1. **Select a category** — Choose which project you're working on (or leave as "General").
2. **Click "Start Focus"** — The timer starts counting up in real-time (HH:MM:SS).
3. **Click "Stop & Save"** — Saves the session with its duration.
4. **Click "Cancel"** — Discards the session without saving.

#### Pomodoro Mode

1. **Switch to Pomodoro** — Click the "Pomodoro" toggle at the top of the timer card.
2. **Configure durations** (optional) — Click the gear icon to adjust:
   - Work duration (default: 25 min)
   - Short break (default: 5 min)
   - Long break (default: 15 min)
   - Blocks before long break (default: 4)
3. **Click "Start Pomodoro"** — The timer counts down from your work duration.
4. **When the block ends** — You'll hear a beep and get a browser notification (if permitted).
5. **Break time** — The timer switches to break mode. Click "Skip Break" to jump to the next work block.
6. **Block counter** — Shows "Block 2 / 4" so you know where you are in the cycle.

The timer persists across page refreshes. If you navigate away and come back, it resumes from where it left off.

Below the timer:
- **Today's stats** — Total focus time and pomodoro count.
- **Today's Sessions** — List of completed sessions with category, time, type (free/pomodoro), and duration.

---

### Daily Log

End-of-day check-in. One entry per day, designed for fast entry.

1. **Set a daily goal** — What's the one thing you want to accomplish today?
2. **List key tasks** — Top 3 planned tasks for the day.
3. **Rate your focus** — Click a number from 1 (distracted) to 5 (deep focus).
4. **Rate your energy** — 1 (drained) to 5 (energized). Optional.
5. **Rate your mood** — 1 (low) to 5 (great). Optional.
6. **Log distractions** — What pulled you away from work?
7. **Write notes** — What went well, what didn't, any blockers.
8. **Review auto-summary** — Shows your total focus time and steps completed today.
9. **Click "Save Log"** — Creates or updates today's log.

#### Anti-Zero-Day

If you rate your energy or mood as 2 or below, an amber banner appears at the top of the page suggesting a single minimal task — the shortest incomplete step across all categories. The idea: on a low-energy day, doing just one thing keeps your streak alive and prevents guilt spirals.

Your last 7 logs are shown below the form with date, goal, energy/mood badges, and focus rating dots.

---

### Weekly Review

A week-by-week summary of your progress, with navigation to view past weeks.

#### Summary Cards

- **Steps Completed** — This week's count with comparison to last week (+/-).
- **Focus Time** — Total hours and minutes, with week-over-week diff.
- **Avg Focus** — Average focus rating across logged days.
- **Energy / Mood** — Average energy and mood ratings for the week.

#### Auto-Generated Insights

The review automatically generates insights like:
- "You completed 40% more steps than last week"
- "Focus time dropped by 45m. Consider more Pomodoro sessions."
- "Active 5/7 days — strong consistency!"
- "Low energy on 3 days with no high-focus days. Prioritize sleep and recovery."

#### Daily Activity Strip

A Mon-Sun grid showing which days were active (green) vs inactive (gray), with per-day step counts, focus minutes, and log indicators.

#### Last Week's Goals

If you set goals the previous week, they appear here with checkboxes to mark whether each was met.

#### Goals & Reflection

Set up to 3 goals for the current week and answer reflection prompts:
- **Biggest win** — What went well?
- **Biggest blocker** — What held you back?
- **Priority for next week** — The one thing that matters most

Click "Save Goals & Reflection" to persist.

#### Category Breakdown & Completed Steps

Shows which categories got attention this week and a full list of completed steps with the day they were finished.

#### Daily Logs

All logs from the week, showing date, energy/mood badges, focus rating dots, daily goal, and notes.

---

### Search

Global search across all your data. Accessible from the sidebar or by clicking the "Search..." bar at the top of the navigation.

- Type at least 2 characters to search.
- Results are grouped by type: **Categories**, **Steps** (searches title and notes), and **Daily Logs** (searches notes, goals, and distractions).
- Each result links directly to the relevant page.

---

### Data & Export

#### JSON Export

Download a complete backup of your entire database as a structured JSON file. Includes all categories, steps (with hierarchy), focus sessions, daily logs, and weekly goals.

#### Markdown Export

Download the current week's review as a formatted `.md` file — useful for pasting into personal notes or sharing progress.

#### Step Templates

Save a category's step structure as a reusable template:
1. **Save a template** — Select a category, name the template, and click "Save Template".
2. **Load a template** — Click "Use" on any saved template, enter a name for the new category, pick a color, and click "Create Category". All steps and sub-steps are copied over.
3. **Delete a template** — Click "Delete" (the original category is unaffected).

---

### Streaks

Streaks are computed automatically from your activity. A day counts as "active" if you did any of:
- Completed at least 1 step
- Finished at least 1 focus session
- Submitted a daily log

The streak counter on the dashboard shows your current streak and best streak ever.

---

### Dark Mode

Click the "Dark Mode" toggle at the bottom of the sidebar to switch between light and dark themes. Your preference is saved in your browser's localStorage and persists across sessions.

---

## Project Structure

```
app/
├── Livewire/
│   ├── Dashboard.php           # Main dashboard with category CRUD, overdue widget, activity timeline
│   ├── CategoryDetail.php      # Step list with CRUD, notes, due dates, status toggling
│   ├── FocusTimer.php          # Free and Pomodoro timer with configurable settings
│   ├── DailyLogPage.php        # Daily check-in: goal, tasks, ratings, anti-zero-day
│   ├── WeeklyReview.php        # Week stats, insights, goal setting, reflection prompts
│   ├── Search.php              # Global search across steps, categories, logs
│   ├── ExportData.php          # JSON and Markdown export
│   └── Templates.php           # Save and load step templates
├── Models/
│   ├── Category.php            # Project/goal grouping
│   ├── Step.php                # Individual tasks (nestable, due dates, overdue logic)
│   ├── FocusSession.php        # Timed work sessions (free/pomodoro)
│   ├── DailyLog.php            # Daily reflection with energy/mood/goal
│   ├── Setting.php             # Key-value settings (pomodoro config, theme)
│   ├── Template.php            # Reusable step structures
│   ├── ActivityLog.php         # Polymorphic activity feed entries
│   └── WeeklyGoal.php          # Per-week goals and reflection
└── Services/
    ├── StreakService.php       # Streak computation logic
    └── AntiZeroDayService.php  # Suggests minimal task on low-energy days

resources/views/
├── layouts/
│   └── app.blade.php           # Base layout with sidebar, search, dark mode toggle
└── livewire/
    ├── dashboard.blade.php
    ├── category-detail.blade.php
    ├── focus-timer.blade.php
    ├── daily-log-page.blade.php
    ├── weekly-review.blade.php
    ├── search.blade.php
    ├── export-data.blade.php
    └── templates.blade.php

database/
├── migrations/                 # 9 tables: categories, steps, focus_sessions, daily_logs,
│                               #   settings, templates, activity_logs, weekly_goals
└── seeders/
    └── CvImprovementSeeder.php # Pre-populates with CV plan data
```
