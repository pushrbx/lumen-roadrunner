<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Contracts\Pipeline\Hub;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindValidationFactoryListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindValidationFactoryListener
 */
class RebindValidationFactoryListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;

        /** @var \Illuminate\Validation\Factory $validator */
        $validator = $this->app->make('validator');

        $this->setProperty($validator, $container_prop = 'container', $app_clone);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($validator, $container_prop));
    }

    /**
     * @return RebindValidationFactoryListener
     */
    protected function listenerFactory(): RebindValidationFactoryListener
    {
        return new RebindValidationFactoryListener();
    }
}
