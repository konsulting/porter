<?php

namespace App\Support\Console\DockerCompose;

class NullCliCommand extends CliCommand
{
    /**
     * Execute the command.
     */
    public function perform(): string|int|null
    {
        return null;
    }
}
