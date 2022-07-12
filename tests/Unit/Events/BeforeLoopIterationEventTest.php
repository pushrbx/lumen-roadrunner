<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Events;

use pushrbx\LumenRoadRunner\Events\Contracts;
use pushrbx\LumenRoadRunner\Events\BeforeLoopIterationEvent;

/**
 * @covers \pushrbx\LumenRoadRunner\Events\BeforeLoopIterationEvent
 */
class BeforeLoopIterationEventTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testInterfacesImplementation(): void
    {
        foreach ([Contracts\WithApplication::class, Contracts\WithServerRequest::class] as $interface) {
            $this->assertContains(
                $interface,
                \class_implements(BeforeLoopIterationEvent::class),
                "Event does not implements [{$interface}]"
            );
        }
    }

    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $event = new BeforeLoopIterationEvent(
            $this->app,
            $request = (new \Nyholm\Psr7\Factory\Psr17Factory())->createServerRequest('GET', 'https://testing')
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($request, $event->serverRequest());
    }
}
