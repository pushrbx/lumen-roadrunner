<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use pushrbx\LumenRoadRunner\Listeners\BindRequestListener;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Events\Contracts\WithHttpRequest;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\BindRequestListener
 */
class BindRequestListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        /** @var Request $modified_request */
        $modified_request = clone $this->app->make('request');
        /** @var Request $original_request */
        $original_request = $this->app->make('request');

        /** @var m\MockInterface|WithApplication|WithHttpRequest $event_mock */
        $event_mock = m::mock(\implode(',', [WithApplication::class, WithHttpRequest::class]))
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock()
            ->expects('httpRequest')
            ->andReturn($modified_request)
            ->getMock();

        $this->listenerFactory()->handle($event_mock);

        $this->assertNotSame($original_request, $this->app->make('request'));
    }

    /**
     * @return BindRequestListener
     */
    protected function listenerFactory(): BindRequestListener
    {
        return new BindRequestListener();
    }
}
