<?php

namespace App\Commands;

use App\Models\Site;

class MakeFiles extends BaseCommand
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
        $wasUp = $this->porter->isUp();

        if ($wasUp) {
            $this->call('stop');
        }

        $this->porter->compose();

        foreach(Site::all() as $site) {
            $site->buildFiles();
        }

        if ($wasUp) {
            $this->call('start');
        }
    }
}
