<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'view_mode',
        'position',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'folder_id' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Get the user that owns the list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks that belong to the list.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'list_id');
    }

    /**
     * Root tasks for the list.
     */
    public function rootTasks(): HasMany
    {
        return $this->tasks()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->orderBy('created_at');
    }
}
