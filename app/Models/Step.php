<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step extends Model
{
    protected $fillable = ['category_id', 'parent_id', 'title', 'notes', 'status', 'sort_order', 'due_date', 'completed_at', 'estimated_minutes', 'document_id'];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Step::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Step::class, 'parent_id')->orderBy('sort_order');
    }

    public function focusSessions(): HasMany
    {
        return $this->hasMany(FocusSession::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function planDays(): HasMany
    {
        return $this->hasMany(StepPlanDay::class)->orderBy('date');
    }

    /** Estimate share for one plan day: explicit minutes, or an even split. */
    public function minutesForDay(?int $override = null): int
    {
        if ($override) {
            return $override;
        }

        $days = $this->relationLoaded('planDays')
            ? $this->planDays->count()
            : ($this->plan_days_count ?? $this->planDays()->count());

        return (int) ceil(($this->estimated_minutes ?? 0) / max(1, $days));
    }

    public function isParent(): bool
    {
        return $this->children()->exists();
    }

    public function markAsDone(): void
    {
        $this->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);
    }

    public function markAsTodo(): void
    {
        $this->update([
            'status' => 'todo',
            'completed_at' => null,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->status !== 'done' && $this->due_date->isPast();
    }

    public function isDueSoon(): bool
    {
        return $this->due_date && $this->status !== 'done' && ! $this->due_date->isPast() && $this->due_date->diffInDays(today()) <= 2;
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')->where('status', '!=', 'done')->where('due_date', '<', today());
    }

    public function scopePlannedBetween($query, $from, $to)
    {
        return $query->whereHas('planDays', fn ($q) => $q->whereBetween('date', [$from, $to]));
    }

    public function scopeUnplanned($query)
    {
        return $query->whereDoesntHave('planDays')->where('status', '!=', 'done');
    }

    public function scopeDueSoon($query)
    {
        return $query->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->where('due_date', '>=', today())
            ->where('due_date', '<=', today()->addDays(2));
    }
}
