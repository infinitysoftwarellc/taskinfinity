<?php

// This service provider configures application services for the app service scope.
namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use MeiliSearch\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', fn ($user = null) => $user !== null);

        $this->configureScoutIndexes();
    }

    /**
     * Configures filterable and sortable attributes for Scout indexes when using Meilisearch.
     */
    protected function configureScoutIndexes(): void
    {
        if (config('scout.driver') !== 'meilisearch') {
            return;
        }

        $indexes = [
            (new Task())->searchableAs() => [
                'filterable' => ['user_id', 'list_id', 'status', 'is_starred'],
                'sortable' => ['due_at', 'position'],
            ],
            (new Project())->searchableAs() => [
                'filterable' => ['user_id', 'folder_id', 'is_pinned'],
                'sortable' => ['name', 'position'],
            ],
        ];

        rescue(function () use ($indexes) {
            /** @var Client $client */
            $client = app(Client::class);

            foreach ($indexes as $name => $settings) {
                $index = $client->index($name);

                if (! empty($settings['filterable'])) {
                    $index->updateFilterableAttributes($settings['filterable']);
                }

                if (! empty($settings['sortable'])) {
                    $index->updateSortableAttributes($settings['sortable']);
                }
            }
        }, report: false);
    }
}
