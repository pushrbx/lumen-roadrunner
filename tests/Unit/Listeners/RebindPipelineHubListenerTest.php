<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Contracts\Pipeline\Hub;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindPipelineHubListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindPipelineHubListener
 */
class RebindPipelineHubListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;

        /** @var \Illuminate\Pipeline\Hub $hub */
        $hub = $this->app->make(Hub::class);

        $this->setProperty($hub, $container_prop = 'container', $app_clone);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($hub, $container_prop));
    }

    /**
     * @return RebindPipelineHubListener
     */
    protected function listenerFactory(): RebindPipelineHubListener
    {
        return new RebindPipelineHubListener();
    }
}
