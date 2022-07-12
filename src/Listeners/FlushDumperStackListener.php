<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

use pushrbx\LumenRoadRunner\Dumper\Stack\StackInterface;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;

/**
 * You should not to register this listener manually.
 *
 * @see \pushrbx\LumenRoadRunner\Dumper\Dumper
 */
class FlushDumperStackListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithApplication) {
            $app = $event->application();

            if (!$app->bound($stack_abstract = StackInterface::class)) {
                return;
            }

            /** @var StackInterface $stack */
            $stack = $app->make($stack_abstract);

            if ($stack->count() > 0) {
                $stack->flush();
            }
        }
    }
}
