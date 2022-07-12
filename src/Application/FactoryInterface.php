<?php

namespace pushrbx\LumenRoadRunner\Application;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;

interface FactoryInterface
{
    /**
     * Create a new application instance.
     *
     * @param string $base_path
     *
     * @return ApplicationContract
     */
    public function create(string $base_path): ApplicationContract;
}
