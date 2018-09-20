<?php

namespace Tests\Feature;

use App\Support\Console\Cli;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Filesystem\Filesystem;
use Tests\BaseTestCase;

class ProviderBaseTest extends BaseTestCase
{
    /** @test */
    public function it_passes_the_correct_ssl_directory_to_the_certificate_builder()
    {
        $certificateBuilder = new CertificateBuilder(new Cli, new Filesystem, '/my/path/ssl');

        $expected = [
            'key'  => '/my/path/ssl/url.key',
            'csr'  => '/my/path/ssl/url.csr',
            'crt'  => '/my/path/ssl/url.crt',
            'conf' => '/my/path/ssl/url.conf',
        ];

        $this->assertSame($expected, (array) $certificateBuilder->paths('url'));
    }
}
