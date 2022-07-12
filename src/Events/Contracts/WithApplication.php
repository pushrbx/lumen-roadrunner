<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events\Contracts;

use Laravel\Lumen\Application;

interface WithApplication
{
    /**
     * Get application instance.
     *
     * @return Application
     */
    public function application(): Application;
}
