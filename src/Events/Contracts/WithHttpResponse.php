<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface WithHttpResponse
{
    /**
     * Get HTTP response instance.
     *
     * @return Response
     */
    public function httpResponse(): Response;
}
