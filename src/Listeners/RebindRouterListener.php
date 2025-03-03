<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use Symfony\Component\HttpKernel\Exception\HttpException;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Events\Contracts\WithHttpRequest;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/RebindRouterContainer.php
 * @link https://github.com/laravel/octane/blob/1.x/src/Listeners/GiveNewApplicationInstanceToRouter.php
 */
class RebindRouterListener implements ListenerInterface
{
    use Traits\InvokerTrait;

    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication && $event instanceof WithHttpRequest) {
            $app     = $event->application();
            $request = $event->httpRequest();

            if (!$app->resolved($router_abstract = 'router')) {
                return;
            }

            /** @var \Illuminate\Routing\Router $router */
            $router = $app->make($router_abstract);

            /**
             * Method `setContainer` for the Router available since Laravel v8.35.0.
             *
             * @link https://git.io/JszyO Source code (v8.35.0)
             * @see  \Illuminate\Routing\Router::setContainer
             */
            if (!$this->invokeMethod($router, 'setContainer', $app)) {
                $this->setProperty($router, 'container', $app);
            }

            try {
                if ($request instanceof \Illuminate\Http\Request && $app instanceof \Illuminate\Container\Container) {
                    $router->getRoutes()->match($request)->setContainer($app);
                }
            } catch (HttpException $e) {
                // do nothing
            }
        }
    }
}
