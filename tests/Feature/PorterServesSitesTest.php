<?php

namespace Tests\Feature;

use App\Porter;
use Illuminate\Support\Facades\Artisan;

class PorterServesSitesTest extends TestCase
{
    protected $porter;

    public function setUp() :void
    {
        parent::setUp();

        $this->porter = app(Porter::class);

        Artisan::call('begin', ['home' => __DIR__.'/../TestWebRoot']);

        Artisan::call('start');

        if (! $this->porter->isUp()) {
            throw new \Exception('Porter could not be started');
        }
    }

    public function tearDown()
    {
        Artisan::call('stop');

        if ($this->porter->isUp()) {
            throw new \Exception('Porter could not be stopped');
        }

        parent::tearDown();
    }

    /**
     * @test
     * @group docker
     */
    public function porter_makes_the_sample_site_available()
    {
        Artisan::call('site:unsecure', ['site' => 'sample']);

        $phpinfo = file_get_contents('http://sample.test');

        $this->assertContains('php', $phpinfo);
    }
}
