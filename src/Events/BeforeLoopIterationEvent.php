<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Events;

use Psr\Http\Message\ServerRequestInterface;
use Laravel\Lumen\Application as ApplicationContract;

final class BeforeLoopIterationEvent implements Contracts\WithApplication, Contracts\WithServerRequest
{
    /**
     * Application instance.
     */
    private ApplicationContract $app;

    /**
     * Raw server request.
     */
    private ServerRequestInterface $request;

    /**
     * Create a new event instance.
     *
     * @param ApplicationContract    $app
     * @param ServerRequestInterface $request
     */
    public function __construct(ApplicationContract $app, ServerRequestInterface $request)
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
    public function serverRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
