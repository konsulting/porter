<?php

namespace App\Support;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Support\Contracts\Cli as CliContract;

class Cli implements CliContract
{
    public function exec($command)
    {
        $process = new Process($command);
        $process->run();

        return $process->getOutput();
    }

    public function execRealTime($command)
    {
        $process = new Process($command);

        try {
            $process->mustRun(function ($type, $buffer) {
                echo $buffer;
            });
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }

    public function passthru($command)
    {
        $process = new Process($command);

        try {
            $process->setTty(true);
            $process->mustRun(function ($type, $buffer) {
                echo $buffer;
            });
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }
}
