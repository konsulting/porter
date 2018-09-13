<?php

namespace Tests\Live;

use Illuminate\Support\Facades\Artisan;
use Tests\LiveTestCase;

class PorterServesSitesTest extends LiveTestCase
{
    /** @test */
    public function porter_makes_the_sample_site_available()
    {
        Artisan::call('site:unsecure', ['site' => 'sample']);

        $phpinfo = $this->get('http://sample.test');

        $this->assertContains('php', $phpinfo);
    }

    /**
     * @test
     */
    public function porter_makes_the_sample_site_available_securely()
    {
        Artisan::call('site:secure', ['site' => 'sample']);

        $phpinfo = $this->get('https://sample.test');

        $this->assertContains('php', $phpinfo);
    }
}
