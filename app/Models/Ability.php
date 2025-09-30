<?php

// This model class represents ability data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'effect_json',
    ];

    protected $casts = [
        'effect_json' => 'array',
    ];

    public function userAbilities()
    {
        return $this->hasMany(UserAbility::class);
    }
}
