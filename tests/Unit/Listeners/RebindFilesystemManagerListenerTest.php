<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Filesystem\FilesystemManager;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindFilesystemManagerListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindFilesystemManagerListener
 */
class RebindFilesystemManagerListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;

        /** @var FilesystemManager $filesystem_manager */
        $filesystem_manager = $this->app->make('filesystem');

        $this->setProperty($filesystem_manager, $app_prop = 'app', $app_clone);

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($filesystem_manager, $app_prop));
    }

    /**
     * @return RebindFilesystemManagerListener
     */
    protected function listenerFactory(): RebindFilesystemManagerListener
    {
        return new RebindFilesystemManagerListener();
    }
}
