<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events\Contracts;

use Throwable;

interface WithException
{
    /**
     * Get exception instance.
     *
     * @return Throwable
     */
    public function exception(): Throwable;
}
