<?php

$driver = env('SEARCH_DRIVER', 'local');

return [
    'driver' => in_array($driver, ['local', 'meilisearch'], true)
        ? $driver
        : 'local',

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),
        'key' => env('MEILISEARCH_KEY'),
    ],
];
