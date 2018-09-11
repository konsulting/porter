<?php

namespace Tests\Unit\Ssl;

use App\Support\Ssl\CertificateBuilder;
use Tests\TestCase;

class CertificateBuilderTest extends TestCase
{
    /**
     * @group certificates
     * @test
     */
    public function it_creates_a_certificate()
    {
        $dir = storage_path('test_library/ssl');

        $builder = new CertificateBuilder($dir);
        $builder->build('klever.test');

        $this->assertFileExists($dir.'/klever.test.conf');
        $this->assertFileExists($dir.'/klever.test.crt');
        $this->assertFileExists($dir.'/klever.test.csr');
        $this->assertFileExists($dir.'/klever.test.key');

        $this->assertFileExists($dir.'/KleverPorterSelfSigned.key');
        $this->assertFileExists($dir.'/KleverPorterSelfSigned.pem');
        $this->assertFileExists($dir.'/KleverPorterSelfSigned.srl');
    }
}
