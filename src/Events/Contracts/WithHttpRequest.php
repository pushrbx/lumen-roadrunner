<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events\Contracts;

use Symfony\Component\HttpFoundation\Request;

interface WithHttpRequest
{
    /**
     * Get HTTP request instance.
     *
     * @return Request
     */
    public function httpRequest(): Request;
}
