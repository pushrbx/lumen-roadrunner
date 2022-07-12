<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindQueueManagerListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindQueueManagerListener
 */
class RebindQueueManagerListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;

        /** @var \Illuminate\Queue\QueueManager $queue_manager */
        $queue_manager = $this->app->make('queue');

        $this->setProperty($queue_manager, $app_prop = 'app', $app_clone);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($queue_manager, $app_prop));
    }

    /**
     * @return RebindQueueManagerListener
     */
    protected function listenerFactory(): RebindQueueManagerListener
    {
        return new RebindQueueManagerListener();
    }
}
