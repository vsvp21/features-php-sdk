<?php

return [
    'url' => env('FEATURE_URL', 'http://localhost:8080'),
    'timeout' => env('FEATURE_TIMEOUT', 3.0),
    'max_tries' => env('FEATURE_MAX_TRIES', 3),
];
