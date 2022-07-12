<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use Illuminate\Config\Repository as ConfigRepository;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;

/**
 * @link https://github.com/swooletw/laravel-swoole/blob/master/src/Server/Resetters/ResetConfig.php
 */
class CloneConfigListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            /** @var ConfigRepository $config */
            $config = $app->make(ConfigRepository::class);

            $app->instance('config', clone $config);
        }
    }
}
