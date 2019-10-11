<?php

namespace App\Support\Console\DockerCompose;

class NullCliCommand extends CliCommand
{
    /**
     * Execute the command.
     *
     * @return string|null
     */
    public function perform()
    {
    }
}
