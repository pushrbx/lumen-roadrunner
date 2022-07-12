<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use Livewire\LivewireManager;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;

/**
 * Target package: <https://github.com/livewire/livewire>.
 */
class ResetLivewireListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if (!\class_exists(LivewireManager::class)) {
            return;
        }

        if ($event instanceof WithApplication) {
            $app = $event->application();

            if (!$app->resolved($manager_abstract = LivewireManager::class)) {
                return;
            }

            /** @var LivewireManager $manager */
            $manager = $app->make($manager_abstract);

            if (\method_exists($manager, 'flushState')) {
                $manager->flushState();
            }
        }
    }
}
