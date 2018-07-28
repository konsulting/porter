<?php

use App\Porter;

function settings($key = null) {
    if (! $key) {
        return app(Porter::class)->getSettings();
    }

    return app(Porter::class)->getSettings()->get($key);
}

function docker_compose($command = null)
{
    return 'docker-compose -f '.base_path('docker-compose.yaml').($command ? ' '.$command : '');
}
