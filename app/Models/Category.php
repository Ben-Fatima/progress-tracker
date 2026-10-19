<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'color', 'deadline', 'priority', 'sort_order', 'archived_at', 'parent_id'];

    public const PRIORITIES = [
        'low' => ['label' => 'Low', 'icon' => '---', 'color' => 'text-gray-400', 'bg' => 'bg-gray-100 dark:bg-gray-800'],
        'normal' => ['label' => 'Normal', 'icon' => '--', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
        'high' => ['label' => 'High', 'icon' => '!', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
        'critical' => ['label' => 'Critical', 'icon' => '!!', 'color' => 'text-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20'],
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'archived_at' => 'datetime',
        ];
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->progress < 100 && $this->deadline->isBefore(today());
    }

    public function isDueSoon(): bool
    {
        return $this->deadline && $this->progress < 100 && ! $this->deadline->isBefore(today()) && $this->deadline->diffInDays(today()) <= 3;
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (! $this->deadline) {
            return null;
        }

        return (int) today()->diffInDays($this->deadline, false);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function focusSessions(): HasMany
    {
        return $this->hasMany(FocusSession::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Aggregate progress across all children (for parent categories).
     */
    public function getChildrenProgressAttribute(): int
    {
        $children = $this->children()->active()->get();
        if ($children->isEmpty()) {
            return $this->progress;
        }

        $totalSteps = 0;
        $completedSteps = 0;
        foreach ($children as $child) {
            $totalSteps += $child->total_steps;
            $completedSteps += $child->completed_steps;
        }
        // Include own steps if any
        $totalSteps += $this->total_steps;
        $completedSteps += $this->completed_steps;

        return $totalSteps > 0 ? (int) round(($completedSteps / $totalSteps) * 100) : 0;
    }

    public function getTotalStepsAttribute(): int
    {
        return $this->steps()->count();
    }

    public function getCompletedStepsAttribute(): int
    {
        return $this->steps()->where('status', 'done')->count();
    }

    public function getProgressAttribute(): int
    {
        $total = $this->total_steps;
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->completed_steps / $total) * 100);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function getPriorityConfigAttribute(): array
    {
        return self::PRIORITIES[$this->priority ?? 'normal'] ?? self::PRIORITIES['normal'];
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw("CASE priority WHEN 'critical' THEN 0 WHEN 'high' THEN 1 WHEN 'normal' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->orderBy('sort_order');
    }
}
