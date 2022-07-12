<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Dumper\Stoppers;

/**
 * @internal
 */
interface StopperInterface
{
    /**
     * Stops the execution.
     *
     * @return void
     */
    public function stop(): void;
}
