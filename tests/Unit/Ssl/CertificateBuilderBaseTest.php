<?php

namespace Tests\Unit\Ssl;

use App\Support\Console\Cli;
use App\Support\Mechanics\Mechanic;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Filesystem\Filesystem;
use Mockery\Mock;
use Tests\BaseTestCase;

class CertificateBuilderBaseTest extends BaseTestCase
{
    protected $dir;

    /**
     * @var Mock|Mechanic
     */
    protected $mechanic;

    /**
     * @var CertificateBuilder
     */
    protected $certificateBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->dir = storage_path('test_library/ssl');
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0755, true);
        }
        $this->mechanic = \Mockery::mock(Mechanic::class);
        $this->certificateBuilder = new CertificateBuilder(new Cli(), new Filesystem(), $this->mechanic, $this->dir);
    }

    /** @test */
    public function it_creates_a_certificate()
    {
        $this->mechanic->shouldReceive('trustCA')
            ->once()
            ->with($this->dir.'/KleverPorterCASelfSigned.pem');

        $this->certificateBuilder->build('klever.test');

        $this->assertHasCertificates();
        $this->assertHasCaCertificates();
    }

    /** @test */
    public function it_removes_a_certificate()
    {
        $this->dummyCerts();

        $this->certificateBuilder->destroy('klever.test');

        $this->assertNoCertificates();
    }

    /** @test */
    public function it_removes_all_certificates_except_ca()
    {
        $this->dummyCaCerts();
        $this->dummyCerts('klever.test');
        $this->dummyCerts('klever-one.test');

        $this->certificateBuilder->clearCertificates(/* $dropCa = false */);

        $this->assertNoCertificates('klever.test');
        $this->assertNoCertificates('klever-one.test');
        $this->assertHasCaCertificates();
    }

    /** @test */
    public function it_removes_all_certificates()
    {
        $this->dummyCaCerts();
        $this->dummyCerts('klever.test');
        $this->dummyCerts('klever-one.test');

        $this->certificateBuilder->clearCertificates($dropCa = true);

        $this->assertNoCertificates('klever.test');
        $this->assertNoCertificates('klever-one.test');
        $this->assertNoCaCertificates();
    }

    protected function dummyCerts($url = 'klever.test')
    {
        @touch($this->dir."/{$url}.conf");
        @touch($this->dir."{$url}.crt");
        @touch($this->dir."/{$url}.csr");
        @touch($this->dir."{$url}.key");
    }

    protected function dummyCaCerts()
    {
        @touch($this->dir.'/KleverPorterCASelfSigned.key');
        @touch($this->dir.'/KleverPorterCASelfSigned.pem');
        @touch($this->dir.'/KleverPorterCASelfSigned.srl');
    }

    protected function assertHasCertificates($url = 'klever.test')
    {
        $this->assertFileExists($this->dir."/{$url}.conf");
        $this->assertFileExists($this->dir."/{$url}.crt");
        $this->assertFileExists($this->dir."/{$url}.csr");
        $this->assertFileExists($this->dir."/{$url}.key");
    }

    protected function assertNoCertificates($url = 'klever.test')
    {
        $this->assertFileNotExists($this->dir."/{$url}.conf");
        $this->assertFileNotExists($this->dir."/{$url}.crt");
        $this->assertFileNotExists($this->dir."/{$url}.csr");
        $this->assertFileNotExists($this->dir."/{$url}.key");
    }

    protected function assertHasCaCertificates()
    {
        $this->assertFileExists($this->dir.'/KleverPorterCASelfSigned.key');
        $this->assertFileExists($this->dir.'/KleverPorterCASelfSigned.pem');
        $this->assertFileExists($this->dir.'/KleverPorterCASelfSigned.srl');
    }

    protected function assertNoCaCertificates()
    {
        $this->assertFileNotExists($this->dir.'/KleverPorterCASelfSigned.key');
        $this->assertFileNotExists($this->dir.'/KleverPorterCASelfSigned.pem');
        $this->assertFileNotExists($this->dir.'/KleverPorterCASelfSigned.srl');
    }
}
