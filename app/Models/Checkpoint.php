<?php

// This model class represents checkpoint data within the application.
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
        'parent_id',
        'parent_checkpoint_id',
        'due_at',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'due_at' => 'datetime',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
