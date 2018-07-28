<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create(['name' => 'home', 'value' => '']);
        Setting::create(['name' => 'tld', 'value' => 'test']);
        Setting::create(['name' => 'db_host', 'value' => 'docker.for.mac.localhost']);
    }
}
