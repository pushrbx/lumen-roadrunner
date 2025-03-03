<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit;

/**
 * @coversNothing
 */
class WorkerBinaryTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    protected string $binary_location = __DIR__ . '/../../bin/rr-worker';

    /**
     * @return void
     */
    public function testFileLocationAndAttributes(): void
    {
        $this->assertFileExists($this->binary_location);
        $this->assertTrue(\is_executable($this->binary_location));
    }

    /**
     * @return void
     */
    public function testExecution(): void
    {
        $this->markTestIncomplete('There is no legal way for execution testing.');
    }
}
