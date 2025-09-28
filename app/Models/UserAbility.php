<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAbility extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ability_id',
        'level',
        'is_equipped',
        'unlocked_at',
    ];

    protected $casts = [
        'is_equipped' => 'boolean',
        'unlocked_at' => 'datetime',
    ];

    public function ability()
    {
        return $this->belongsTo(Ability::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
