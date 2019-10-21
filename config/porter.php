<?php

return [
    /* The default set of images to use when installing porter. */
    'default-docker-image-set' => 'konsulting/porter-ubuntu',

    /* The path to use for the Porter Library. */
    'library_path' => env('LIBRARY_PATH'),

    /*
     * The timeout in seconds for the Porter PHP processes that wrap the console process.
     * We default to 1200 seconds (20 minutes) but if you want to run the node container
     * or others for a while, you may prefer to make it infinite - using null.
     */
    'process_timeout' => 1200,
];
