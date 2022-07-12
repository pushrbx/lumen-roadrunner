<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner;

use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Get config root key name.
     *
     * @return string roadrunner
     */
    public static function getConfigRootKey(): string
    {
        return \basename(static::getConfigPath(), '.php');
    }

    /**
     * Returns path to the configuration file.
     *
     * @return string
     */
    public static function getConfigPath(): string
    {
        return __DIR__ . '/../config/roadrunner.php';
    }

    /**
     * Register package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->initializeConfigs();

        $this->app->singleton(Dumper\Stack\StackInterface::class, Dumper\Stack\FixedArrayStack::class);
        $this->app->singleton(Dumper\Dumper::class, Dumper\Dumper::class);

        $this->app
            ->when(Dumper\Dumper::class)
            ->needs(Dumper\Stoppers\StopperInterface::class)
            ->give(Dumper\Stoppers\OsExit::class);
    }

    /**
     * Boot package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootEventListeners();
        $this->bootMiddlewares();
    }

    /**
     * @return void
     */
    protected function bootEventListeners(): void
    {
        /** @var array<class-string, array<class-string>> $config_listeners */
        $config_listeners = (array) $this->app['config']->get(static::getConfigRootKey(), '.listeners');
        $events = app('events');

        foreach ($config_listeners as $event => $listeners) {
            foreach (\array_filter(\array_unique($listeners)) as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * @return void
     */
    protected function bootMiddlewares(): void
    {
        if ($this->app instanceof LumenApplication) {
            $this->app->middleware([Dumper\Middleware::class]);
        }
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs(): void
    {
        $this->mergeConfigFrom(static::getConfigPath(), static::getConfigRootKey());

        $this->publishes([
            \realpath(static::getConfigPath()) => config_path(\basename(static::getConfigPath())),
        ], 'config');
    }
}
