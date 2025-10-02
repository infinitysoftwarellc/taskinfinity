<?php

// This model class represents mission data within the application.
namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'list_id',
        'title',
        'priority',
        'labels_json',
        'is_starred',
        'status',
        'completed_at',
        'position',
        'xp_reward',
        'due_at',
    ];

    protected $casts = [
        'labels_json' => 'array',
        'is_starred' => 'boolean',
        'due_at' => 'immutable_datetime:UTC',
        'completed_at' => 'immutable_datetime:UTC',
    ];

    protected function dueAt(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => $value ? CarbonImmutable::parse($value, 'UTC') : null,
            set: static function ($value) {
                if ($value instanceof CarbonInterface) {
                    return $value->copy()->setTimezone('UTC');
                }

                return $value ? CarbonImmutable::parse($value, 'UTC') : null;
            }
        );
    }

    protected static function booted(): void
    {
        static::saving(function (self $mission): void {
            if (! $mission->isDirty('status')) {
                return;
            }

            if ($mission->status === 'done') {
                if (! $mission->completed_at) {
                    $mission->completed_at = now();
                }

                return;
            }

            $mission->completed_at = null;
        });
    }

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
