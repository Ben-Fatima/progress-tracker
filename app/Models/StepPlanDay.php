<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepPlanDay extends Model
{
    protected $fillable = ['step_id', 'date', 'minutes'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
