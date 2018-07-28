<?php

namespace App\Commands;

use App\Porter;
use App\Site;
use LaravelZero\Framework\Commands\Command;

class MakeFiles extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make-files';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '(Re)make the files we need';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $porter = app(Porter::class);

        $wasUp = $porter->isUp();

        if ($wasUp) {
            $this->call('stop');
        }

        $porter->compose();

        foreach(Site::all() as $site) {
            $site->buildFiles();
        }

        if ($wasUp) {
            $this->call('start');
        }
    }
}
