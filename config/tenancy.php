<?php

return [
    'central_domains' => array_filter(array_map('trim', explode(',', env('CENTRAL_DOMAINS', '127.0.0.1,localhost')))),
    'storage_route_prefix' => 'storage',
];
