<?php

// This model class represents habit streak cache data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitStreakCache extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'habit_streaks_cache';

    protected $fillable = [
        'habit_id',
        'current_streak',
        'longest_streak',
        'last_checkin_local',
        'updated_at',
    ];

    protected $casts = [
        'last_checkin_local' => 'date',
        'updated_at' => 'datetime',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}
