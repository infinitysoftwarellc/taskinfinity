<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitCheckin extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'habit_id',
        'checked_on_local',
        'created_at',
    ];

    protected $casts = [
        'checked_on_local' => 'date',
        'created_at' => 'datetime',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}
