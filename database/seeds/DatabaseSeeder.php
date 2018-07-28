<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(SettingsSeeder::class);
        $this->call(PhpVersionsSeeder::class);
    }
}
