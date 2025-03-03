<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Laravel\Scout\EngineManager as ScoutEngineManager;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\ResetLaravelScoutListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\ResetLaravelScoutListener
 */
class ResetLaravelScoutListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @see \Laravel\Scout\ScoutServiceProvider::register() */
        $this->app->singleton(ScoutEngineManager::class);

        /** @var ScoutEngineManager $scout */
        $scout = $this->app->make(ScoutEngineManager::class);

        // burn drivers property
        $scout->driver($scout->getDefaultDriver());

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->assertNotEmpty($this->getProperty($scout, $drivers_prop = 'drivers'));

        $this->listenerFactory()->handle($event_mock);

        $this->assertEmpty($this->getProperty($scout, $drivers_prop));
    }

    /**
     * @return ResetLaravelScoutListener
     */
    protected function listenerFactory(): ResetLaravelScoutListener
    {
        return new ResetLaravelScoutListener();
    }
}
