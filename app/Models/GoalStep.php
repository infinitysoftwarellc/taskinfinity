<?php

// This model class represents goal step data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'is_done',
        'position',
        'note',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function goal()
    {
        return $this->belongsTo(BigGoal::class, 'goal_id');
    }
}
