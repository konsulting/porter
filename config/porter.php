<?php

return [
    /* The default set of images to use when installing porter */
    'default-docker-image-set' => 'konsulting/porter-ubuntu',

    'library_path' => env('LIBRARY_PATH', storage_path('test_library')),

    'docker-compose-file' => base_path('docker-compose.yaml'),
];
