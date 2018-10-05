<?php

namespace Tests\Feature;

use App\Support\Contracts\Cli as CliContract;
use App\Support\Mechanics\Mechanic;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Tests\BaseTestCase;

class ProviderBaseTest extends BaseTestCase
{
    /** @test */
    public function it_passes_the_correct_ssl_directory_to_the_certificate_builder()
    {
        $mechanic = Mockery::spy(Mechanic::class);
        $cli = Mockery::spy(CliContract::class);
        $certificateBuilder = new CertificateBuilder($cli, new Filesystem(), $mechanic, '/my/path/ssl');

        $expected = [
            'key'  => '/my/path/ssl/url.key',
            'csr'  => '/my/path/ssl/url.csr',
            'crt'  => '/my/path/ssl/url.crt',
            'conf' => '/my/path/ssl/url.conf',
        ];

        $this->assertSame($expected, (array) $certificateBuilder->paths('url'));
    }
}
