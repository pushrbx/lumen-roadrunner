<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Dumper\Stoppers;

/**
 * @internal
 * @codeCoverageIgnore
 */
final class Noop implements StopperInterface
{
    /**
     * {@inheritDoc}
     */
    public function stop(): void
    {
        // do nothing
    }
}
