<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Illuminate\Support\Str;
use pushrbx\LumenRoadRunner\Listeners\FlushStrCacheListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\FlushStrCacheListener
 */
class FlushStrCacheListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        Str::snake('Hello world');
        Str::camel('Hello world');
        Str::studly('Hello world');

        $this->assertNotEmpty($this->getStaticProperty($class = Str::class, 'snakeCache'));
        $this->assertNotEmpty($this->getStaticProperty($class, 'camelCache'));
        $this->assertNotEmpty($this->getStaticProperty($class, 'studlyCache'));

        $this->listenerFactory()->handle(new \stdClass());

        $this->assertEmpty($this->getStaticProperty($class, 'snakeCache'));
        $this->assertEmpty($this->getStaticProperty($class, 'camelCache'));
        $this->assertEmpty($this->getStaticProperty($class, 'studlyCache'));
    }

    /**
     * @return FlushStrCacheListener
     */
    protected function listenerFactory(): FlushStrCacheListener
    {
        return new FlushStrCacheListener();
    }
}
