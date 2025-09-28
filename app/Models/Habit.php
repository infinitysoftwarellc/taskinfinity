<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'color',
        'icon',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkins()
    {
        return $this->hasMany(HabitCheckin::class);
    }

    public function monthlyStats()
    {
        return $this->hasMany(HabitMonthlyStat::class);
    }

    public function streakCache()
    {
        return $this->hasOne(HabitStreakCache::class);
    }
}
