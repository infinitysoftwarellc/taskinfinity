<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Throwable;

trait HasLocalSearch
{
    /**
     * Determine if the Meilisearch driver is active.
     */
    protected static function usesMeilisearch(): bool
    {
        return config('search.driver') === 'meilisearch';
    }

    /**
     * Execute a search using the configured driver, falling back to the local strategy when needed.
     *
     * @param  string  $query
     * @param  callable|null  $callback
     * @return \Laravel\Scout\Builder|EloquentBuilder
     */
    public static function search($query = '', $callback = null)
    {
        if (static::usesMeilisearch()) {
            try {
                $builder = static::scoutSearch($query, $callback);

                return static::decorateScoutBuilder($builder, $query);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return static::localSearchQuery($query);
    }

    /**
     * Provide the local query builder fallback for models.
     */
    abstract protected static function localSearchQuery(string $query): EloquentBuilder;

    /**
     * Allow concrete models to customize the Scout builder before usage.
     *
     * @param  mixed  $builder
     * @param  string  $query
     * @return mixed
     */
    protected static function decorateScoutBuilder($builder, string $query)
    {
        return $builder;
    }

    /**
     * Dispatch queued syncing when the external driver is enabled.
     */
    public function queueMakeSearchable($models)
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutQueueMakeSearchable($models), report: false);
    }

    /**
     * Synchronize search indexes immediately when the external driver is enabled.
     */
    public function syncMakeSearchable($models)
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutSyncMakeSearchable($models), report: false);
    }

    /**
     * Dispatch queued removal when the external driver is enabled.
     */
    public function queueRemoveFromSearch($models)
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutQueueRemoveFromSearch($models), report: false);
    }

    /**
     * Synchronize removal from the index immediately when the external driver is enabled.
     */
    public function syncRemoveFromSearch($models)
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutSyncRemoveFromSearch($models), report: false);
    }

    /**
     * Make the current model instance searchable when the external driver is enabled.
     */
    public function searchable()
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutSearchable(), report: false);
    }

    /**
     * Immediately make the current model searchable when the external driver is enabled.
     */
    public function searchableSync()
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutSearchableSync(), report: false);
    }

    /**
     * Remove the current model from the search index when the external driver is enabled.
     */
    public function unsearchable()
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutUnsearchable(), report: false);
    }

    /**
     * Immediately remove the current model from the search index when the external driver is enabled.
     */
    public function unsearchableSync()
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => $this->scoutUnsearchableSync(), report: false);
    }

    /**
     * Make all models searchable when the external driver is enabled.
     */
    public static function makeAllSearchable($chunk = null)
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => static::scoutMakeAllSearchable($chunk), report: false);
    }

    /**
     * Remove all models from the search index when the external driver is enabled.
     */
    public static function removeAllFromSearch()
    {
        if (! static::usesMeilisearch()) {
            return;
        }

        rescue(fn () => static::scoutRemoveAllFromSearch(), report: false);
    }

    /**
     * Determine whether the model should be searchable.
     */
    public function shouldBeSearchable()
    {
        if (! static::usesMeilisearch()) {
            return false;
        }

        return rescue(fn () => $this->scoutShouldBeSearchable(), report: false) ?? false;
    }

    /**
     * Determine if the model existed in the index before updating.
     */
    public function wasSearchableBeforeUpdate()
    {
        if (! static::usesMeilisearch()) {
            return false;
        }

        return rescue(fn () => $this->scoutWasSearchableBeforeUpdate(), report: false) ?? false;
    }

    /**
     * Determine if the model existed in the index before deletion.
     */
    public function wasSearchableBeforeDelete()
    {
        if (! static::usesMeilisearch()) {
            return false;
        }

        return rescue(fn () => $this->scoutWasSearchableBeforeDelete(), report: false) ?? false;
    }
}
