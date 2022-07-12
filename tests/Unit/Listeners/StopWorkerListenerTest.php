<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Spiral\RoadRunner\Http\PSR7Worker;
use pushrbx\LumenRoadRunner\Listeners\StopWorkerListener;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\StopWorkerListener
 */
class StopWorkerListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $worker = m::mock()->shouldReceive('stop')->once()->getMock();

        $psr7_client_mock = new class($worker) {
            protected $worker;

            public function __construct($worker)
            {
                $this->worker = $worker;
            }

            public function getWorker()
            {
                return $this->worker;
            }
        };

        $this->app->instance(PSR7Worker::class, $psr7_client_mock);

        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->once()
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);
    }

    /**
     * {@inheritdoc}
     */
    protected function listenerFactory()
    {
        return new StopWorkerListener();
    }
}
