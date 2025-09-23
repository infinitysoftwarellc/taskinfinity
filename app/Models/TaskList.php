<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'folder_id',
        'user_id',
        'parent_id',
        'name',
    ];

    /**
     * Get the user that owns the task list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the folder that the task list belongs to.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Get the tasks for the task list.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
    /**
     * Get the parent list.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaskList::class, 'parent_id');
    }

    /**
     * Get the child lists (sub-lists).
     */
    public function children(): HasMany
    {
        return $this->hasMany(TaskList::class, 'parent_id');
    }
}