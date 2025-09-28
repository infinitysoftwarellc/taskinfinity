<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RitualEntry extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ritual_id',
        'date_local',
        'completed',
        'created_at',
    ];

    protected $casts = [
        'date_local' => 'date',
        'completed' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function ritual()
    {
        return $this->belongsTo(Ritual::class);
    }
}
