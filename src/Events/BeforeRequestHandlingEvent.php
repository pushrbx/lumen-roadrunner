<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events;

use Symfony\Component\HttpFoundation\Request;
use Laravel\Lumen\Application as ApplicationContract;

final class BeforeRequestHandlingEvent implements Contracts\WithApplication, Contracts\WithHttpRequest
{
    /**
     * Application instance.
     */
    private ApplicationContract $app;

    /**
     * Incoming request.
     */
    private Request $request;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract $app
     * @param Request             $request
     */
    public function __construct(ApplicationContract $app, Request $request)
    {
        $this->app     = $app;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function application(): ApplicationContract
    {
        return $this->app;
    }

    /**
     * {@inheritdoc}
     */
    public function httpRequest(): Request
    {
        return $this->request;
    }
}
