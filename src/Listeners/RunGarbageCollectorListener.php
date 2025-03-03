<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Listeners;

class RunGarbageCollectorListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        \gc_collect_cycles(); // keep the memory usage low (this will slow down your application a bit)
    }
}
