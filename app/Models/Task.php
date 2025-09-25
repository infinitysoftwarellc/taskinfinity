<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'list_id',
        'title',
        'description',
        'due_at',
        'priority',
        'status',
        'estimate_pomodoros',
        'pomodoros_done',
        'position',
        'completed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'list_id' => 'integer',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimate_pomodoros' => 'integer',
        'pomodoros_done' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Get the list that owns the task.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(TaskList::class, 'list_id');
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
