<?php

// This model class represents big goal data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BigGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'progress_percent',
        'created_by_ai',
    ];

    protected $casts = [
        'created_by_ai' => 'boolean',
    ];

    public function steps()
    {
        return $this->hasMany(GoalStep::class, 'goal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
