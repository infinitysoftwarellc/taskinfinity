<?php

namespace App\Models;

use App\Models\Concerns\HasLocalSearch;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Project extends TaskList
{
    use Searchable, HasLocalSearch {
        Searchable::search as protected scoutSearch;
        Searchable::queueMakeSearchable as protected scoutQueueMakeSearchable;
        Searchable::syncMakeSearchable as protected scoutSyncMakeSearchable;
        Searchable::queueRemoveFromSearch as protected scoutQueueRemoveFromSearch;
        Searchable::syncRemoveFromSearch as protected scoutSyncRemoveFromSearch;
        Searchable::searchable as protected scoutSearchable;
        Searchable::searchableSync as protected scoutSearchableSync;
        Searchable::unsearchable as protected scoutUnsearchable;
        Searchable::unsearchableSync as protected scoutUnsearchableSync;
        Searchable::makeAllSearchable as protected scoutMakeAllSearchable;
        Searchable::removeAllFromSearch as protected scoutRemoveAllFromSearch;
        Searchable::shouldBeSearchable as protected scoutShouldBeSearchable;
        Searchable::wasSearchableBeforeUpdate as protected scoutWasSearchableBeforeUpdate;
        Searchable::wasSearchableBeforeDelete as protected scoutWasSearchableBeforeDelete;
        HasLocalSearch::search insteadof Searchable;
        HasLocalSearch::queueMakeSearchable insteadof Searchable;
        HasLocalSearch::syncMakeSearchable insteadof Searchable;
        HasLocalSearch::queueRemoveFromSearch insteadof Searchable;
        HasLocalSearch::syncRemoveFromSearch insteadof Searchable;
        HasLocalSearch::searchable insteadof Searchable;
        HasLocalSearch::searchableSync insteadof Searchable;
        HasLocalSearch::unsearchable insteadof Searchable;
        HasLocalSearch::unsearchableSync insteadof Searchable;
        HasLocalSearch::makeAllSearchable insteadof Searchable;
        HasLocalSearch::removeAllFromSearch insteadof Searchable;
        HasLocalSearch::shouldBeSearchable insteadof Searchable;
        HasLocalSearch::wasSearchableBeforeUpdate insteadof Searchable;
        HasLocalSearch::wasSearchableBeforeDelete insteadof Searchable;
    }

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
