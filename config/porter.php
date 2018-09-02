<?php

return [
    /* The default set of images to use when installing porter */
    'default-docker-image-set' => 'konsulting/porter-ubuntu',

    'library_path' => determineLibraryPath(),

    'docker-compose-file' =>determineLibraryPath().'/docker-compose.yaml',
];
