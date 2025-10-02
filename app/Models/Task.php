<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class Task extends Mission
{
    use Searchable;

    protected $table = 'missions';

    /**
     * Get the Scout index name for the model.
     */
    public function searchableAs(): string
    {
        return config('scout.prefix', '').'tasks';
    }

    /**
     * Prepare the data array for Scout indexing.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getKey(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_at' => $this->due_at?->toIso8601String(),
            'list_id' => $this->list_id,
            'is_starred' => (bool) $this->is_starred,
        ];
    }
}
