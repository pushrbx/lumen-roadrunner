<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use Illuminate\Routing\UrlGenerator;
use pushrbx\LumenRoadRunner\ServiceProvider;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Events\Contracts\WithHttpRequest;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * This listener must be registered BEFORE `SetServerPortListener` for correct links generation.
 *
 * @see SetServerPortListener
 */
class ForceHttpsListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithHttpRequest && $event instanceof WithApplication) {
            $app = $event->application();

            /** @var ConfigRepository $config */
            $config = $app->make(ConfigRepository::class);

            if ((bool) $config->get(ServiceProvider::getConfigRootKey() . '.force_https', false)) {
                if ($app->resolved($url_generator_abstract = UrlGenerator::class)) {
                    /** @var UrlGenerator $url_generator */
                    $url_generator = $app->make($url_generator_abstract);
                    $url_generator->forceScheme('https');
                }

                // Set 'HTTPS' server parameter (required for correct working request methods like ::isSecure
                // and others)
                $event->httpRequest()->server->set('HTTPS', 'on');
            }
        }
    }
}
