<?php

namespace Tests;

use App\Porter;
use Illuminate\Support\Facades\Artisan;

class LiveTestCase extends BaseTestCase
{
    /** @var Porter */
    protected $porter;

    public function setUp() :void
    {
        parent::setUp();

        Artisan::call('start');

        $this->assertTrue($this->porter->isUp(), 'Porter could not start.');
    }

    public function tearDown()
    {
        Artisan::call('stop');

        $this->assertFalse($this->porter->isUp(), 'Porter is still up and should have stopped.');

        parent::tearDown();
    }

    protected function preparePorter()
    {
        $this->porter = $this->app[Porter::class];

        Artisan::call('begin', ['home' => __DIR__.'/TestWebRoot', '--force' => true]);
        Artisan::call('db:seed');
    }

    /**
     * @param string $url
     *
     * @throws \Exception
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|mixed
     */
    public function get($url, array $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Since curl is a bit dodgy with the DNS for now, we
        // force it to resolve to 127.0.0.1
        curl_setopt($ch, CURLOPT_RESOLVE, $this->buildResolveOption($url));

        // Since the cert won't be trusted, ignore it
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $phpinfo = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Curl failed. '.curl_error($ch));
        }
        curl_close($ch);

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
