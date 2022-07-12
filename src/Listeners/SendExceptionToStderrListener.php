<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use pushrbx\LumenRoadRunner\Events\Contracts\WithException;

class SendExceptionToStderrListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithException) {
            \fwrite(\STDERR, (string) $event->exception());
        }
    }
}
