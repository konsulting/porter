<?php

namespace Tests\Ssl;

use App\Ssl\CertificateBuilder;
use Tests\TestCase;

class CertificateBuilderTest extends TestCase
{
    /** @test */
    public function it_creates_a_certificate()
    {
        $dir = storage_path('test_ssl');

        @mkdir($dir);

        $builder = new CertificateBuilder($dir);
        $builder->build('klever.test');

        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'klever.test.conf');
        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'klever.test.crt');
        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'klever.test.csr');
        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'klever.test.key');

        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'KleverPorterSelfSigned.key');
        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'KleverPorterSelfSigned.pem');
        $this->assertFileExists($dir.DIRECTORY_SEPARATOR.'KleverPorterSelfSigned.srl');

        $this->cleanseDir($dir);
    }
}
