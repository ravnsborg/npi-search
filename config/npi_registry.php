<?php

return [
    'url' => env('NPI_REGISTRY_URL'),
    'version' => env('NPI_REGISTRY_VERSION'),
    'timeout' => (int) env('NPI_REGISTRY_TIMEOUT', 10),
];
