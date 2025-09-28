<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'criteria_json',
        'reward_xp',
    ];

    protected $casts = [
        'criteria_json' => 'array',
    ];

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }
}
