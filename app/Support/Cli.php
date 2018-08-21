<?php

namespace App\Support;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Cli
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
