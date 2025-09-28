<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'title',
        'is_done',
        'position',
        'xp_reward',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
