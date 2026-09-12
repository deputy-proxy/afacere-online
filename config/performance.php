<?php

return [
    'slow_query_ms' => (float) env('PERFORMANCE_SLOW_QUERY_MS', 500),
    'deployment_id' => env('APP_VERSION', env('GITHUB_SHA', 'local')),
];
