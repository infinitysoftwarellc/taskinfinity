<?php

namespace App\Models;

use App\Models\Concerns\HasLocalSearch;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Project extends TaskList
{
    use Searchable {
        search as protected scoutSearch;
        queueMakeSearchable as protected scoutQueueMakeSearchable;
        syncMakeSearchable as protected scoutSyncMakeSearchable;
        queueRemoveFromSearch as protected scoutQueueRemoveFromSearch;
        syncRemoveFromSearch as protected scoutSyncRemoveFromSearch;
        searchable as protected scoutSearchable;
        searchableSync as protected scoutSearchableSync;
        unsearchable as protected scoutUnsearchable;
        unsearchableSync as protected scoutUnsearchableSync;
        makeAllSearchable as protected scoutMakeAllSearchable;
        removeAllFromSearch as protected scoutRemoveAllFromSearch;
        shouldBeSearchable as protected scoutShouldBeSearchable;
        wasSearchableBeforeUpdate as protected scoutWasSearchableBeforeUpdate;
        wasSearchableBeforeDelete as protected scoutWasSearchableBeforeDelete;
    }
    use HasLocalSearch;

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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'color' => $this->color,
            'view_type' => $this->view_type,
            'is_pinned' => (bool) $this->is_pinned,
            'folder_id' => $this->folder_id,
        ];
    }

    /**
     * Execute a scoped Scout search limited to the authenticated owner/workspace.
     */
    protected static function localSearchQuery(string $query): EloquentBuilder
    {
        $query = trim($query);
        $userId = Auth::id();

        if (! $userId) {
            return static::query()->whereRaw('1 = 0');
        }

        return static::query()
            ->where('user_id', $userId)
            ->when($query !== '', function (EloquentBuilder $builder) use ($query) {
                $builder->where(function (EloquentBuilder $builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('view_type', 'like', "%{$query}%");
                });
            })
            ->orderBy('name');
    }

    /**
     * Apply authenticated scoping to the Scout builder when Meilisearch is active.
     */
    protected static function decorateScoutBuilder($builder, string $query)
    {
        if ($userId = Auth::id()) {
            $builder->where('user_id', $userId);
        }

        return $builder;
    }
}
