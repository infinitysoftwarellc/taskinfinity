<?php

namespace App\Models\Concerns;

use Illuminate\Support\Arr;

trait HasFeatureFlags
{
    /**
     * Determine if a given feature flag is enabled.
     */
    public function hasFeature(string $feature): bool
    {
        return (bool) Arr::get(config('services.features', []), $feature, false);
    }

    /**
     * Check if the application is running in demo mode.
     */
    public function inDemoMode(): bool
    {
        return (bool) data_get(config('services.demo', []), 'enabled', false);
    }
}
