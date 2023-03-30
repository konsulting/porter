<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\Models\PhpVersion;

class ListVersions extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List available sites';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $headers = ['Version Number', 'Default', 'Active'];

        $activeVersions = PhpVersion::active()->get();

        $versions = PhpVersion::orderBy('name', 'asc')
            ->get()
            ->map(fn($version) => [
                $version->version_number,
                $version->default ? 'yes' : '-',
                $activeVersions->contains($version) ? 'yes' : '-',
            ]);

        $this->table($headers, $versions);
    }
}
