<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Application;

use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\Lumen\Application as ApplicationContract;

/**
 * @internal
 */
final class Factory implements FactoryInterface
{
    /**
     * @param string $base_path
     *
     * @return ApplicationContract
     * @throws BindingResolutionException
     */
    public function create(string $base_path): ApplicationContract
    {
        $path = \implode(\DIRECTORY_SEPARATOR, [\rtrim($base_path, \DIRECTORY_SEPARATOR), 'bootstrap', 'app.php']);

        if (!\is_file($path)) {
            throw new \InvalidArgumentException("Application bootstrap file was not found in [{$path}]");
        }

        /** @var ApplicationContract $app */
        $app = require $path;

        return $app;
    }
}
