<?php

use App\PhpVersion;
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
        PhpVersion::create(['version_number' => '7.0', 'default' => true]);
        PhpVersion::create(['version_number' => '5.6', 'default' => false]);
    }
}
