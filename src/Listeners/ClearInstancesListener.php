<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use Illuminate\Container\Container;
use pushrbx\LumenRoadRunner\ServiceProvider;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/ClearInstances.php
 */
class ClearInstancesListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            if ($app instanceof Container) {
                /** @var ConfigRepository $config */
                $config = $app->make(ConfigRepository::class);

                /** @var array<string> $abstracts */
                $abstracts = (array) $config->get(ServiceProvider::getConfigRootKey() . '.clear', []);

                foreach ($abstracts as $abstract) {
                    $app->forgetInstance($abstract);
                }
            }
        }
    }
}
