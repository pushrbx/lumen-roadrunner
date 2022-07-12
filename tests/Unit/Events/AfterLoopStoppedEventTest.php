<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Events;

use pushrbx\LumenRoadRunner\Events\Contracts;
use pushrbx\LumenRoadRunner\Events\AfterLoopStoppedEvent;

/**
 * @covers \pushrbx\LumenRoadRunner\Events\AfterLoopStoppedEvent
 */
class AfterLoopStoppedEventTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testInterfacesImplementation(): void
    {
        foreach ([Contracts\WithApplication::class] as $interface) {
            $this->assertContains(
                $interface,
                \class_implements(AfterLoopStoppedEvent::class),
                "Event does not implements [{$interface}]"
            );
        }
    }

    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $event = new AfterLoopStoppedEvent($this->app);

        $this->assertSame($this->app, $event->application());
    }
}
