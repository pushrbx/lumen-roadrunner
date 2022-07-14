<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner;

use Illuminate\Config\Repository as ConfigRepository;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function bootEventListeners(): void
    {
        /** @var ConfigRepository $config */
        $config = $this->app->get('config');
        /** @var array<class-string, array<class-string>> $config_listeners */
        $config_listeners = (array) $config->get(static::getConfigRootKey() . '.listeners');
        /** @var EventsDispatcher $events */
        $events = $this->app->make(EventsDispatcher::class);

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
            \realpath(static::getConfigPath()) => $this->app->configPath(\basename(static::getConfigPath())),
        ], 'config');
    }
}
