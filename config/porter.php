<?php

use App\Support\Mechanics\ChooseMechanic;

return [
    /* The default set of images to use when installing porter */
    'default-docker-image-set' => 'konsulting/porter-ubuntu',

    'library_path' => ChooseMechanic::forOS()->getUserHomePath().'/.porter',

    'docker-compose-file' =>ChooseMechanic::forOS()->getUserHomePath().'/.porter/docker-compose.yaml',
];
