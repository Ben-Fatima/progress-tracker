<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SavingsGoal extends Model
{
    protected $fillable = ['title', 'description', 'target_amount', 'color', 'is_active', 'target_date'];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'target_date' => 'date',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SavingsTransaction::class)->orderByDesc('date');
    }

    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getCurrentBalanceAttribute(): float
    {
        return (float) $this->transactions()->sum('amount');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_balance / (float) $this->target_amount) * 100, 1));
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->target_amount - $this->current_balance);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->target_amount > 0 && $this->current_balance >= (float) $this->target_amount;
    }
}
