<?php

namespace Tests\Unit;

use App\PhpVersion;
use App\Site;
use Tests\TestCase;

class PhpVersionTest extends TestCase
{
    /** @test */
    public function it_returns_a_safe_version_number()
    {
        $version = factory(PhpVersion::class)->create([
            'version_number' => "7.2",
        ]);

        $this->assertEquals("7-2", $version->safe);
    }

    /** @test */
    public function we_can_set_the_default_version()
    {
        factory(PhpVersion::class, 4)->create();

        PhpVersion::setDefaultVersion(2);

        $this->assertCount(1, PhpVersion::whereDefault(true)->get());
        $this->assertEquals(true, PhpVersion::find(2)->default);

        $this->assertEquals(2, PhpVersion::defaultVersion()->id);
    }

    /** @test */
    public function it_finds_a_version_with_user_input()
    {
        factory(PhpVersion::class)->create([
            'version_number' => "7.2",
        ]);

        $this->assertEquals("7.2", PhpVersion::findByDirtyVersionNumber('7_2')->version_number);
    }

    /** @test */
    public function it_returns_the_active_versions()
    {
        $v5 = factory(PhpVersion::class)->create(['version_number' => '5.6']);

        $v7 = factory(PhpVersion::class)->states(['default'])
            ->create(['version_number' => '7.0']);

        factory(Site::class)->create(['php_version_id' => $v5->id]);

        $active = PhpVersion::active()->get();

        $this->assertCount(2, $active);
        $this->assertContains($v5->id, $active->pluck('id'));
        $this->assertContains($v7->id, $active->pluck('id'));
    }
}
