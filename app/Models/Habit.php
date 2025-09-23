<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'frequency',
        'start_date',
        'goal_days',
    ];

    public function completions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }
}