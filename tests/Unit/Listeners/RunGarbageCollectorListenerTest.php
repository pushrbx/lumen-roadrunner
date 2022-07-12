<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use pushrbx\LumenRoadRunner\Listeners\RunGarbageCollectorListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RunGarbageCollectorListener
 */
class RunGarbageCollectorListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $this->listenerFactory()->handle(new \stdClass());

        $this->markTestIncomplete('There is no legal way for handle method testing.');
    }

    /**
     * @return RunGarbageCollectorListener
     */
    protected function listenerFactory(): RunGarbageCollectorListener
    {
        return new RunGarbageCollectorListener();
    }
}
