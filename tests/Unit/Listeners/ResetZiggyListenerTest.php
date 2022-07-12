<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Tightenco\Ziggy\BladeRouteGenerator;
use pushrbx\LumenRoadRunner\Listeners\ResetZiggyListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\ResetZiggyListener
 */
class ResetZiggyListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        BladeRouteGenerator::$generated = true;

        $this->listenerFactory()->handle(new \stdClass());

        $this->assertFalse(BladeRouteGenerator::$generated);
    }

    /**
     * @return ResetZiggyListener
     */
    protected function listenerFactory(): ResetZiggyListener
    {
        return new ResetZiggyListener();
    }
}
