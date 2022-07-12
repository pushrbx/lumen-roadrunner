<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\UnqueueCookiesListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\UnqueueCookiesListener
 */
class UniqueCookiesListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @var \Illuminate\Cookie\CookieJar $cookies */
        $cookies = $this->app->make('cookie');

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $cookies->queue('foo', 'one');
        $cookies->queue('bar', 'two');

        $this->assertNotEmpty($cookies->getQueuedCookies());

        $this->listenerFactory()->handle($event_mock);

        $this->assertEmpty($cookies->getQueuedCookies());
    }

    /**
     * @return UnqueueCookiesListener
     */
    protected function listenerFactory(): UnqueueCookiesListener
    {
        return new UnqueueCookiesListener();
    }
}
