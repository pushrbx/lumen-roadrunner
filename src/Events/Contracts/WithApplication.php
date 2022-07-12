<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events\Contracts;

use Illuminate\Contracts\Foundation\Application;

interface WithApplication
{
    /**
     * Get application instance.
     *
     * @return Application
     */
    public function application(): Application;
}
