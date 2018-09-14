<?php

namespace Tests\Feature;

use App\Support\Ssl\CertificateBuilder;
use Tests\BaseTestCase;

class ProviderBaseTest extends BaseTestCase
{
    /** @test */
    public function it_passes_the_correct_ssl_directory_to_the_certificate_builder()
    {
        $certificateBuilder = new CertificateBuilder('/my/path/ssl');

        $expected = [
            'key' => '/my/path/ssl/url.key',
            'csr' => '/my/path/ssl/url.csr',
            'crt' => '/my/path/ssl/url.crt',
            'conf' => '/my/path/ssl/url.conf',
        ];

        $this->assertSame($expected, (array) $certificateBuilder->paths('url'));
    }
}
