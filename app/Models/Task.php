<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    public const MAX_DEPTH = 6;

    protected $fillable = [
        'user_id',
        'list_id',
        'parent_id',
        'depth',
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
        'parent_id' => 'integer',
        'depth' => 'integer',
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

    /**
     * Parent task relation.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Children tasks relation.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('position')
            ->orderBy('created_at');
    }

    /**
     * Recursive children relation.
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }
}
