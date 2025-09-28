<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'list_id',
        'title',
        'description',
        'priority',
        'labels_json',
        'is_starred',
        'status',
        'position',
        'xp_reward',
        'due_at',
    ];

    protected $casts = [
        'labels_json' => 'array',
        'is_starred' => 'boolean',
        'due_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function list()
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
