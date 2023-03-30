<?php

namespace App\Commands\Valet;

use App\Commands\BaseCommand;
use App\Support\Valet\Valet;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'valet:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Let Porter know it is not running alongside Laravel Valet';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        app(Valet::class)->turnOff();
    }
}
