<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Support\Str;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use pushrbx\LumenRoadRunner\Listeners\FlushDatabaseQueryLogListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\FlushDatabaseQueryLogListener
 */
class FlushDatabaseQueryLogListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @var ConfigRepository $config */
        $config = $this->app->make(ConfigRepository::class);

        $config->set('database.connections.' . ($connection_name = 'test_' . Str::random()), [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        /** @var \Illuminate\Database\Connection $connection */
        $connection = $this->app->make('db')->connection($connection_name);

        $connection->enableQueryLog();
        $connection->logQuery('select $1', ['1' => '1']);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->assertNotEmpty($connection->getQueryLog());

        $this->listenerFactory()->handle($event_mock);

        $this->assertEmpty($connection->getQueryLog());
    }

    /**
     * @return FlushDatabaseQueryLogListener
     */
    protected function listenerFactory(): FlushDatabaseQueryLogListener
    {
        return new FlushDatabaseQueryLogListener();
    }
}
