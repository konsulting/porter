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
        Setting::create(['name' => 'domain', 'value' => 'test']);
        Setting::create(['name' => 'use_mysql', 'value' => 'on']);
        Setting::create(['name' => 'use_redis', 'value' => 'on']);
        Setting::create(['name' => 'use_browser', 'value' => 'on']);
        Setting::create(['name' => 'host_machine_name', 'value' => 'host.docker.internal']);
    }
}
