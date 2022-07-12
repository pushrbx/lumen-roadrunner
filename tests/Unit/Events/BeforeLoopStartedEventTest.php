<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Events;

use pushrbx\LumenRoadRunner\Events\Contracts;
use pushrbx\LumenRoadRunner\Events\BeforeLoopStartedEvent;

/**
 * @covers \pushrbx\LumenRoadRunner\Events\BeforeLoopStartedEvent
 */
class BeforeLoopStartedEventTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testInterfacesImplementation(): void
    {
        foreach ([Contracts\WithApplication::class] as $interface) {
            $this->assertContains(
                $interface,
                \class_implements(BeforeLoopStartedEvent::class),
                "Event does not implements [{$interface}]"
            );
        }
    }

    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $event = new BeforeLoopStartedEvent($this->app);

        $this->assertSame($this->app, $event->application());
    }
}
