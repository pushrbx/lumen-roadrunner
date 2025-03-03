<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Events\Contracts\WithHttpRequest;
use pushrbx\LumenRoadRunner\Events\Contracts\WithHttpResponse;
use Laravel\Lumen\Application as ApplicationContract;

final class AfterRequestHandlingEvent implements WithApplication, WithHttpRequest, WithHttpResponse
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
     * Outgoing response.
     */
    private Response $response;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract $app
     * @param Request             $request
     * @param Response            $response
     */
    public function __construct(ApplicationContract $app, Request $request, Response $response)
    {
        $this->app      = $app;
        $this->request  = $request;
        $this->response = $response;
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

    /**
     * {@inheritdoc}
     */
    public function httpResponse(): Response
    {
        return $this->response;
    }
}
