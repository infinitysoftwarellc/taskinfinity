<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'parent_id',
        'due_date',
        'priority',
        'is_completed',
        'completed_at',
        'pomodoros_completed',
        'pomodoros_estimated',
        'pomodoro_minutes_total',
        'wont_do',
        'is_pinned',
        'user_id',
        'organization_id',
        'project_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
        'wont_do' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }

    // Relação para subtarefas (uma tarefa tem muitas subtarefas)
    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    // Relação para a tarefa pai (uma subtarefa pertence a uma tarefa)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // Relação Muitos-para-Muitos com Tags
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}