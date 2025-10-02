<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class Project extends TaskList
{
    use Searchable;

    protected $table = 'lists';

    /**
     * Get the Scout index name for the model.
     */
    public function searchableAs(): string
    {
        return config('scout.prefix', '').'projects';
    }

    /**
     * Prepare the data array for Scout indexing.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'color' => $this->color,
            'view_type' => $this->view_type,
            'is_pinned' => (bool) $this->is_pinned,
            'folder_id' => $this->folder_id,
        ];
    }
}
