<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Events;

use Illuminate\Http\Request;
use pushrbx\LumenRoadRunner\Events\Contracts;
use pushrbx\LumenRoadRunner\Events\BeforeRequestHandlingEvent;

/**
 * @covers \pushrbx\LumenRoadRunner\Events\BeforeRequestHandlingEvent
 */
class BeforeRequestHandlingEventTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testInterfacesImplementation(): void
    {
        foreach ([Contracts\WithApplication::class, Contracts\WithHttpRequest::class] as $interface) {
            $this->assertContains(
                $interface,
                \class_implements(BeforeRequestHandlingEvent::class),
                "Event does not implements [{$interface}]"
            );
        }
    }

    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $event = new BeforeRequestHandlingEvent(
            $this->app,
            $request = Request::create('/')
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($request, $event->httpRequest());
    }
}
