<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = ['name', 'icon', 'frequency', 'sort_order', 'active'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function isCompletedOn(string $date): bool
    {
        return $this->logs()->where('date', $date)->where('completed', true)->exists();
    }

    public function currentStreak(): int
    {
        $streak = 0;
        $date = today();

        while (true) {
            $hasLog = $this->logs()->where('date', $date->toDateString())->where('completed', true)->exists();
            if (!$hasLog) break;
            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
