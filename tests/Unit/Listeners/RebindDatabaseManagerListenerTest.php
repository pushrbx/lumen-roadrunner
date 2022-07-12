<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Database\DatabaseManager;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindDatabaseManagerListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindDatabaseManagerListener
 */
class RebindDatabaseManagerListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;

        /** @var DatabaseManager $database_manager */
        $database_manager = $this->app->make('db');

        $this->setProperty($database_manager, $app_prop = 'app', $app_clone);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($database_manager, $app_prop));
    }

    /**
     * @return RebindDatabaseManagerListener
     */
    protected function listenerFactory(): RebindDatabaseManagerListener
    {
        return new RebindDatabaseManagerListener();
    }
}
