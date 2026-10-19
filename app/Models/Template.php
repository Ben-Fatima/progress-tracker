<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'description', 'steps'];

    protected function casts(): array
    {
        return [
            'steps' => 'array',
        ];
    }
}
