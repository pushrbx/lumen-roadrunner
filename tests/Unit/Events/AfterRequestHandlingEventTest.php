<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Events;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use pushrbx\LumenRoadRunner\Events\Contracts;
use pushrbx\LumenRoadRunner\Events\AfterRequestHandlingEvent;

/**
 * @covers \pushrbx\LumenRoadRunner\Events\AfterRequestHandlingEvent
 */
class AfterRequestHandlingEventTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testInterfacesImplementation(): void
    {
        $expected = [
            Contracts\WithApplication::class,
            Contracts\WithHttpRequest::class,
            Contracts\WithHttpResponse::class,
        ];

        foreach ($expected as $interface) {
            $this->assertContains(
                $interface,
                \class_implements(AfterRequestHandlingEvent::class),
                "Event does not implements [{$interface}]"
            );
        }
    }

    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $event = new AfterRequestHandlingEvent(
            $this->app,
            $request = Request::create('/'),
            $response = new Response()
        );

        $this->assertSame($this->app, $event->application());
        $this->assertSame($request, $event->httpRequest());
        $this->assertSame($response, $event->httpResponse());
    }
}
