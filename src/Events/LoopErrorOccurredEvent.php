<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use pushrbx\LumenRoadRunner\Events\Contracts\WithException;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Events\Contracts\WithServerRequest;
use Laravel\Lumen\Application as ApplicationContract;

final class LoopErrorOccurredEvent implements WithApplication, WithException, WithServerRequest
{
    /**
     * Application instance.
     */
    private ApplicationContract $app;

    /**
     * Loop error.
     */
    private Throwable $exception;

    /**
     * Raw server request.
     */
    private ServerRequestInterface $server_request;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract    $app
     * @param ServerRequestInterface $server_request
     * @param Throwable              $exception
     */
    public function __construct(ApplicationContract $app, ServerRequestInterface $server_request, Throwable $exception)
    {
        $this->app            = $app;
        $this->server_request = $server_request;
        $this->exception      = $exception;
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
    public function exception(): Throwable
    {
        return $this->exception;
    }

    /**
     * {@inheritdoc}
     */
    public function serverRequest(): ServerRequestInterface
    {
        return $this->server_request;
    }
}
