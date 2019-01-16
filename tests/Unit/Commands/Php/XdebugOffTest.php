<?php

namespace Tests\Unit\Commands\Browser;

use App\Models\PhpVersion;
use App\Support\Php\Xdebug;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class XdebugOffTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_xdebug_off()
    {
        $mock = Mockery::mock(Xdebug::class);
        $mock->shouldReceive('turnOff')->once()->with(null);

        $this->porter->shouldReceive('restart')->once();

        $this->swap(Xdebug::class, $mock);

        $this->artisan('php:xdebug-off');
    }

    /** @test */
    public function it_turns_xdebug_off_for_a_specific_php_version()
    {
        $php = factory(PhpVersion::class)->create();

        $mock = Mockery::mock(Xdebug::class);
        $mock->shouldReceive('turnOff')->once()->with(
            Mockery::on(function (PhpVersion $argument) use ($php) {
                return $argument->getKey() === $php->getKey();
            })
        );

        $this->porter->shouldReceive('restart')->once()->with($php->fpm_name);

        $this->swap(Xdebug::class, $mock);

        $this->artisan('php:xdebug-off', ['php_version' => $php->version_number]);
    }

    /** @test */
    public function it_will_error_when_passed_an_invalid_php_version()
    {
        $this->artisan('php:xdebug-off', ['php_version' => '5.6']);

        $this->assertContains('Invalid PHP version provided', Artisan::output());
    }
}
