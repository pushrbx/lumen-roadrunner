<?php

namespace pushrbx\LumenRoadRunner\Tests\Unit;

use pushrbx\LumenRoadRunner\WorkerOptions;
use pushrbx\LumenRoadRunner\WorkerOptionsInterface;

/**
 * @covers \pushrbx\LumenRoadRunner\WorkerOptions<extended>
 */
class WorkerOptionsTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(WorkerOptionsInterface::class, new WorkerOptions(""));
    }

    /**
     * @return void
     */
    public function testGetters(): void
    {
        $options = new WorkerOptions("foo", true, "bar");

        $this->assertSame("foo", $options->getAppBasePath());
        $this->assertTrue($options->getRefreshApp());
        $this->assertSame("bar", $options->getRelayDsn());
    }
}
