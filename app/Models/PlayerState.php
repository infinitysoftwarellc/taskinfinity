<?php

// This model class represents player state data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerState extends Model
{
    use HasFactory;

    protected $table = 'player_state';

    protected $fillable = [
        'user_id',
        'level',
        'xp_total',
        'life_current',
        'life_max',
        'energy_current',
        'energy_max',
        'last_energy_calc_at',
        'last_life_calc_at',
        'last_daily_reset_at',
    ];

    protected $casts = [
        'last_energy_calc_at' => 'datetime',
        'last_life_calc_at' => 'datetime',
        'last_daily_reset_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
