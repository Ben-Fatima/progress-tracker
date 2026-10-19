<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FocusSession extends Model
{
    protected $fillable = ['category_id', 'step_id', 'type', 'started_at', 'paused_at', 'ended_at', 'paused_seconds', 'duration_minutes', 'duration_seconds', 'outcome', 'difficulty', 'xp_earned'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration_seconds' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function pause(): void
    {
        if (! $this->paused_at) {
            $this->update(['paused_at' => now()]);
        }
    }

    public function resume(): void
    {
        if ($this->paused_at) {
            $pausedFor = (int) $this->paused_at->diffInSeconds(now(), absolute: true);
            $this->update([
                'paused_seconds' => $this->paused_seconds + $pausedFor,
                'paused_at' => null,
            ]);
        }
    }

    public function isPaused(): bool
    {
        return $this->paused_at !== null;
    }

    public function stop(): void
    {
        // If paused when stopping, add final paused time
        $totalPaused = $this->paused_seconds;
        if ($this->paused_at) {
            $totalPaused += (int) $this->paused_at->diffInSeconds(now(), absolute: true);
        }

        $endedAt = now();
        $totalSeconds = (int) $this->started_at->diffInSeconds($endedAt, absolute: true);
        $activeSeconds = max(60, $totalSeconds - $totalPaused);
        $minutes = (int) round($activeSeconds / 60);

        $this->update([
            'ended_at' => $endedAt,
            'paused_at' => null,
            'paused_seconds' => $totalPaused,
            'duration_minutes' => $minutes,
            'duration_seconds' => $activeSeconds,
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function scopeRunning($query)
    {
        return $query->whereNull('ended_at');
    }

    public function durationInSeconds(): int
    {
        return (int) ($this->duration_seconds ?? (($this->duration_minutes ?? 0) * 60));
    }

    public function durationLabel(): string
    {
        return self::formatDuration($this->durationInSeconds());
    }

    public static function formatDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return trim(sprintf('%s%s%s',
            $hours > 0 ? $hours.'h ' : '',
            $minutes > 0 || $hours > 0 ? $minutes.'m ' : '',
            $remainingSeconds.'s'
        ));
    }
}
