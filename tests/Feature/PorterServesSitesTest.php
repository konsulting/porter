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

        $phpinfo = $this->get('http://sample.test');

        $this->assertContains('php', $phpinfo);
    }

    protected function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Since curl is a bit dodgy with the DNS for now, we
        // force it to resolve to 127.0.0.1
        curl_setopt($ch, CURLOPT_RESOLVE, $this->buildResolveOption($url));

        $phpinfo = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Curl failed. '.curl_error($ch));
        }
        curl_close ($ch);

        return $phpinfo;
    }

    protected function buildResolveOption($url)
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);

        $port = $scheme == 'http' ? 80 : 443;

        return ["{$host}:{$port}:127.0.0.1"];
    }
}
