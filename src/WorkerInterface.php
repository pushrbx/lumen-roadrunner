<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner;

interface WorkerInterface
{
    /**
     * Start worker loop.
     *
     * @param WorkerOptionsInterface $options
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function start(WorkerOptionsInterface $options): void;
}
