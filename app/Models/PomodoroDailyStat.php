<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PomodoroDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_local',
        'sessions_count',
        'work_seconds',
        'break_seconds',
    ];

    protected $casts = [
        'date_local' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
