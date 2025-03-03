<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;

/**
 * @link https://github.com/laravel/octane/blob/1.x/src/Listeners/GiveNewApplicationInstanceToDatabaseSessionHandler.php
 */
class RebindDatabaseSessionHandlerListener implements ListenerInterface
{
    use Traits\InvokerTrait;

    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            if (!$app->resolved($session_abstract = 'session')) {
                return;
            }

            /** @var \Illuminate\Session\SessionManager $session */
            $session = $app->make($session_abstract);
            $driver  = $session->driver();

            if ($driver instanceof \Illuminate\Contracts\Session\Session) {
                $handler = $driver->getHandler();

                if ($handler instanceof \Illuminate\Session\DatabaseSessionHandler) {
                    /**
                     * Method `setContainer` for the DatabaseSessionHandler available since Laravel v8.45.0.
                     *
                     * @link https://git.io/JZYrJ Source code (v8.45.0)
                     * @see  \Illuminate\Session\DatabaseSessionHandler::setContainer
                     */
                    if (!$this->invokeMethod($handler, 'setContainer', $app)) {
                        $this->setProperty($handler, 'container', $app);
                    }
                }
            }
        }
    }
}
