<?php

namespace App\Support\Mechanics;

class Windows extends Untrained
{
    /**
     * Return the User's home directory path.
     *
     * @return string
     */
    public function getUserHomePath()
    {
        return $this->serverBag->get('HOME') ?? $this->serverBag->get('HOMEDRIVE').$this->serverBag->get('HOMEPATH');
    }

    /**
     * Flush the host system DNS cache.
     *
     * @return void
     */
    public function flushDns()
    {
        $this->consoleWriter->info('Flushing DNS.');
        $this->cli->passthru('ipconfig /flushdns');
    }
}
