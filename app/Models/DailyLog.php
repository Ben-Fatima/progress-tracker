<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    protected $fillable = [
        'date', 'energy_mode', 'capacity_override', 'daily_goal', 'key_tasks', 'selected_step_ids',
        'focus_rating', 'energy_level', 'mood', 'mood_note',
        'distractions', 'notes',
        'sleep_hours', 'sleep_quality', 'sleep_note', 'slept_at', 'woke_at', 'stress_level',
        'training_done', 'recovery_quality', 'nutrition_quality',
        'acne_level', 'new_breakouts', 'breakout_zones',
        'am_skincare', 'sunscreen', 'pm_skincare_last_night',
        'retinol_last_night', 'on_period', 'skin_note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'key_tasks' => 'array',
            'selected_step_ids' => 'array',
            'sleep_hours' => 'decimal:1',
            'training_done' => 'boolean',
            'breakout_zones' => 'array',
            'am_skincare' => 'boolean',
            'sunscreen' => 'boolean',
            'pm_skincare_last_night' => 'boolean',
            'retinol_last_night' => 'boolean',
            'on_period' => 'boolean',
        ];
    }

    public function scopeRecent($query, int $limit = 7)
    {
        return $query->orderByDesc('date')->limit($limit);
    }

    public static function today(): ?self
    {
        return static::whereDate('date', today()->toDateString())->first();
    }

    public function getIsLowEnergyAttribute(): bool
    {
        return $this->energy_level !== null && $this->energy_level <= 2;
    }

    public function getIsLowMoodAttribute(): bool
    {
        return $this->mood !== null && $this->mood <= 2;
    }
}
