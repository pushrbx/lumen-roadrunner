<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use pushrbx\LumenRoadRunner\Listeners\ListenerInterface;
use pushrbx\LumenRoadRunner\Listeners\SendExceptionToStderrListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\SendExceptionToStderrListener
 */
class SendExceptionToStderrListenerTest extends AbstractListenerTestCase
{
    /**
     * @return void
     */
    public function testHandle(): void
    {
        $this->listenerFactory()->handle(new \stdClass());

        $this->markTestIncomplete('There is no legal way for handle method testing.');
    }

    /**
     * @return SendExceptionToStderrListener
     */
    protected function listenerFactory(): ListenerInterface
    {
        return new SendExceptionToStderrListener();
    }
}
