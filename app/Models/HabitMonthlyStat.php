<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitMonthlyStat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'habit_id',
        'year',
        'month',
        'days_done_count',
        'total_checkins',
        'best_streak_in_month',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}
