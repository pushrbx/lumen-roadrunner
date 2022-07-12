<?php

namespace pushrbx\LumenRoadRunner\Tests\Unit\Console\Commands;

use Mockery as m;
use pushrbx\LumenRoadRunner\WorkerOptions;
use pushrbx\LumenRoadRunner\WorkerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use pushrbx\LumenRoadRunner\Console\Commands\StartCommand;

/**
 * @covers \pushrbx\LumenRoadRunner\Console\Commands\StartCommand<extended>
 *
 * @group  foo
 */
class StartCommandTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testCommandName(): void
    {
        $this->assertSame('start', (new StartCommand())->getName());
    }

    /**
     * @return void
     */
    public function testCommandOptions(): void
    {
        $definitions = (new StartCommand())->getDefinition();

        $option_laravel_path = $definitions->getOption('laravel-path');
        $this->assertNull($option_laravel_path->getShortcut());
        $this->assertTrue($option_laravel_path->isValueOptional());

        $option_relay_dsn = $definitions->getOption('relay-dsn');
        $this->assertNull($option_relay_dsn->getShortcut());
        $this->assertFalse($option_relay_dsn->isValueOptional());
        $this->assertSame('pipes', $option_relay_dsn->getDefault());

        $option_refresh_app = $definitions->getOption('refresh-app');
        $this->assertNull($option_refresh_app->getShortcut());
        $this->assertFalse($option_refresh_app->acceptValue());

        $option_worker_mode = $definitions->getOption('worker-mode');
        $this->assertNull($option_worker_mode->getShortcut());
        $this->assertTrue($option_worker_mode->acceptValue());
        $this->assertSame('auto', $option_worker_mode->getDefault());
    }

    /**
     * @return void
     */
    public function testCommandExecuting(): void
    {
        $this->markTestSkipped('There is now legal way for the execution method testing');
    }
}
