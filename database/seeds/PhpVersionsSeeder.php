<?php

use App\Models\PhpVersion;
use Illuminate\Database\Seeder;

class PhpVersionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhpVersion::create(['version_number' => '5.6', 'default' => false]);
        PhpVersion::create(['version_number' => '7.0', 'default' => false]);
        PhpVersion::create(['version_number' => '7.1', 'default' => false]);
        PhpVersion::create(['version_number' => '7.2', 'default' => false]);
        PhpVersion::create(['version_number' => '7.3', 'default' => true]);
    }
}
