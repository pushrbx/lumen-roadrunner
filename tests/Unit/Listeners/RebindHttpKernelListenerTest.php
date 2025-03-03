<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindHttpKernelListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindHttpKernelListener
 */
class RebindHttpKernelListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @var HttpKernel $kernel */
        $kernel = $this->app->make(HttpKernel::class);

        // Set "wrong" app instance in kernel
        $this->setProperty($kernel, 'app', clone $this->app);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->assertNotSame($this->app, $kernel->getApplication());

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $kernel->getApplication());
    }

    /**
     * @return RebindHttpKernelListener
     */
    protected function listenerFactory(): RebindHttpKernelListener
    {
        return new RebindHttpKernelListener();
    }
}
