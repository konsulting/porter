<?php

namespace App\Commands\Valet;

use App\Support\Valet\Valet;
use App\Commands\BaseCommand;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'valet:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Let Porter know it is running alongside Laravel Valet';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Valet::class)->turnOn();
    }
}
