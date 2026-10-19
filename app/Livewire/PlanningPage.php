<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Step;
use App\Models\StepPlanDay;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PlanningPage extends Component
{
    /** Minutes you can realistically work, by day type. */
    public const WEEKDAY_CAPACITY = 90;

    public const WEEKEND_CAPACITY = 240;

    /**
     * Months shown, including the current one. The plan runs to Dec 21, so from
     * late September that needs 4 tabs (Sep tail + Oct + Nov + Dec).
     */
    public const WINDOW_MONTHS = 4;

    public string $monthCursor = '';

    public ?int $selectedStepId = null;

    public ?int $categoryFilter = null;

    public string $search = '';

    /** Date (Y-m-d) whose day-detail modal is open, or null. */
    public ?string $openDate = null;

    public function mount(): void
    {
        $this->monthCursor = CarbonImmutable::today()->format('Y-m');
    }

    private function windowStart(): CarbonImmutable
    {
        return CarbonImmutable::today()->startOfMonth();
    }

    private function windowEnd(): CarbonImmutable
    {
        return $this->windowStart()->addMonths(self::WINDOW_MONTHS - 1)->endOfMonth();
    }

    private function cursor(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('Y-m-d', $this->monthCursor.'-01')->startOfMonth();
    }

    public function previousMonth(): void
    {
        $target = $this->cursor()->subMonth();
        if ($target >= $this->windowStart()) {
            $this->monthCursor = $target->format('Y-m');
        }
    }

    public function nextMonth(): void
    {
        $target = $this->cursor()->addMonth();
        if ($target <= $this->windowEnd()) {
            $this->monthCursor = $target->format('Y-m');
        }
    }

    public function goToMonth(string $month): void
    {
        $this->monthCursor = $month;
    }

    public function selectStep(int $stepId): void
    {
        $this->selectedStepId = $this->selectedStepId === $stepId ? null : $stepId;
    }

    /** Place the selected step on a day; call again on other days to split it. */
    public function placeOnDay(string $date): void
    {
        if (! $this->selectedStepId) {
            return;
        }

        StepPlanDay::firstOrCreate([
            'step_id' => $this->selectedStepId,
            'date' => $date,
        ]);

        // Keep the step selected so it can be spread across more days.
    }

    /**
     * Clicking a day places the selected step; with nothing selected it
     * opens that day's detail modal instead.
     */
    public function handleDayClick(string $date): void
    {
        $this->selectedStepId ? $this->placeOnDay($date) : $this->openDay($date);
    }

    public function openDay(string $date): void
    {
        $this->openDate = $date;
    }

    public function closeDay(): void
    {
        $this->openDate = null;
    }

    /** Remove a single planned day from a step. */
    public function unschedule(int $stepId, string $date): void
    {
        StepPlanDay::where('step_id', $stepId)->where('date', $date)->delete();
    }

    public function clearMonth(): void
    {
        StepPlanDay::whereBetween('date', [
            $this->cursor()->startOfMonth()->toDateString(),
            $this->cursor()->endOfMonth()->toDateString(),
        ])->delete();
    }

    public function toggleStatus(int $stepId): void
    {
        $step = Step::findOrFail($stepId);
        $step->status === 'done' ? $step->markAsTodo() : $step->markAsDone();
    }

    public static function capacityFor(CarbonImmutable $day): int
    {
        return $day->isWeekend() ? self::WEEKEND_CAPACITY : self::WEEKDAY_CAPACITY;
    }

    /** Effective minutes a plan-day entry occupies. */
    private static function entryMinutes(StepPlanDay $day): int
    {
        return $day->minutes ?? $day->step->minutesForDay();
    }

    public function render()
    {
        $cursor = $this->cursor();
        $gridStart = $cursor->startOfMonth()->startOfWeek(CarbonImmutable::MONDAY);
        $gridEnd = $cursor->endOfMonth()->endOfWeek(CarbonImmutable::MONDAY);

        // Every planned day in the visible grid, keyed by date.
        $planDays = StepPlanDay::with(['step.category', 'step.planDays'])
            ->whereBetween('date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($d) => $d->date->toDateString());

        // Build the calendar weeks.
        $weeks = [];
        $day = $gridStart;
        while ($day <= $gridEnd) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $day->toDateString();
                $entries = $planDays->get($key, collect());
                $week[] = [
                    'date' => $day,
                    'key' => $key,
                    'inMonth' => $day->month === $cursor->month,
                    'isToday' => $day->isToday(),
                    'isPast' => $day->isBefore(CarbonImmutable::today()),
                    'entries' => $entries,
                    'minutes' => (int) $entries->sum(fn ($e) => self::entryMinutes($e)),
                    'capacity' => self::capacityFor($day),
                ];
                $day = $day->addDay();
            }
            $weeks[] = $week;
        }

        // Backlog: steps with no planned day yet.
        $backlog = Step::with('category')
            ->unplanned()
            ->whereDoesntHave('children')
            ->whereHas('category', fn ($q) => $q->whereNull('archived_at'))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn ($s) => $s->category->name);

        // Per-month totals across the whole window.
        $months = [];
        for ($i = 0; $i < self::WINDOW_MONTHS; $i++) {
            $m = $this->windowStart()->addMonths($i);
            $minutes = StepPlanDay::with('step.planDays')
                ->whereBetween('date', [$m->startOfMonth()->toDateString(), $m->endOfMonth()->toDateString()])
                ->get()
                ->sum(fn ($d) => self::entryMinutes($d));

            $months[] = [
                'key' => $m->format('Y-m'),
                'label' => $m->format('M Y'),
                'minutes' => (int) $minutes,
                'isCurrent' => $m->format('Y-m') === $this->monthCursor,
            ];
        }

        $plannedMinutes = (int) StepPlanDay::with('step.planDays')
            ->whereBetween('date', [$this->windowStart()->toDateString(), $this->windowEnd()->toDateString()])
            ->get()
            ->sum(fn ($d) => self::entryMinutes($d));

        return view('livewire.planning-page', [
            'weeks' => $weeks,
            'backlog' => $backlog,
            'months' => $months,
            'monthLabel' => $cursor->format('F Y'),
            'canGoBack' => $cursor->subMonth() >= $this->windowStart(),
            'canGoForward' => $cursor->addMonth() <= $this->windowEnd(),
            'selectedStep' => $this->selectedStepId ? Step::with(['category', 'planDays'])->find($this->selectedStepId) : null,
            'unplannedMinutes' => (int) Step::unplanned()->whereDoesntHave('children')->sum('estimated_minutes'),
            'plannedMinutes' => $plannedMinutes,
            'categories' => Category::active()->ordered()->get(),
            'dayEntries' => $this->openDate
                ? StepPlanDay::with(['step.category', 'step.planDays'])
                    ->where('date', $this->openDate)
                    ->orderBy('id')
                    ->get()
                : collect(),
        ]);
    }
}
