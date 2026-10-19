<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyGoal extends Model
{
    protected $fillable = ['week_start', 'goals', 'met', 'biggest_win', 'biggest_blocker', 'priority_next_week'];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'goals' => 'array',
            'met' => 'array',
        ];
    }

    public static function forWeek($weekStart): ?self
    {
        return static::whereDate('week_start', $weekStart->toDateString())->first();
    }

    public static function currentWeek(): ?self
    {
        return static::forWeek(today()->startOfWeek());
    }

    public static function previousWeek(): ?self
    {
        return static::forWeek(today()->startOfWeek()->subWeek());
    }
}
