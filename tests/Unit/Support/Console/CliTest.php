<?php

namespace Tests\Unit\Support\Console;

use App\Support\Console\Cli;
use Mockery;
use Symfony\Component\Process\Process;
use Tests\BaseTestCase;

class CliTest extends BaseTestCase
{
    /**
     * @test
     * @dataProvider methodProvider
     */
    public function it_sets_the_timeout_on_the_process_object($method)
    {
        $this->app->bind(Process::class, function ($app, $args) {
            $this->assertSame(config('porter.process_timeout'), $args['timeout']);

            return Mockery::mock(Process::class)
                ->shouldReceive('run', 'mustRun', 'getOutput', 'setTty')
                ->getMock();
        });

        app()->make(Cli::class)->$method('foo');
    }

    public function methodProvider()
    {
        return [
            ['exec'],
            ['passthru'],
            ['execRealTime'],
        ];
    }

    /** @test */
    public function it_executes_a_command()
    {
        $this->assertSame('foo'.PHP_EOL, (new Cli())->exec('echo foo'));
        $this->assertSame('foo'.PHP_EOL, $this->captureOutput(function () {
            app()->make(Cli::class)->execRealTime('echo foo');
        }));
    }

    /** @test */
    public function a_cli_instance_has_no_timeout_when_created()
    {
        $this->assertNull((new Cli())->getTimeout());
    }

    /** @test */
    public function a_cli_instance_made_by_the_app_has_the_default_timeout()
    {
        $this->assertSame(config('porter.process_timeout'), app()->make(Cli::class)->getTimeout());
    }

    /** @test */
    public function the_timeout_for_a_cli_instance_can_be_changed_and_removed()
    {
        $cli = new Cli();

        $this->assertNull($cli->getTimeout());

        $cli->setTimeout(60);
        $this->assertSame(60, $cli->getTimeout());

        $cli->doNotTimeout();
        $this->assertNull($cli->getTimeout());
    }

    /**
     * Run the callback and return the captured output.
     *
     * @param callable $callback
     *
     * @return false|string
     */
    protected function captureOutput($callback)
    {
        ob_start();
        $callback();

        return ob_get_clean();
    }
}
