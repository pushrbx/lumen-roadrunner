<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface WithServerRequest
{
    /**
     * Get server request instance.
     *
     * @return ServerRequestInterface
     */
    public function serverRequest(): ServerRequestInterface;
}
