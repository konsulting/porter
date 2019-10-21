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
