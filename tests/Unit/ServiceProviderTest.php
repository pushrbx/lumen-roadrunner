<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit;

use pushrbx\LumenRoadRunner\Dumper\Dumper;
use pushrbx\LumenRoadRunner\ServiceProvider;
use pushrbx\LumenRoadRunner\Dumper\Middleware;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use pushrbx\LumenRoadRunner\Dumper\Stack\StackInterface;
use pushrbx\LumenRoadRunner\Dumper\Stack\FixedArrayStack;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * @covers \pushrbx\LumenRoadRunner\ServiceProvider
 */
class ServiceProviderTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    /**
     * @return void
     */
    public function testGetConfigRootKey(): void
    {
        $this->assertSame('roadrunner', ServiceProvider::getConfigRootKey());
    }

    /**
     * @return void
     */
    public function testGetConfigPath(): void
    {
        $this->assertSame(
            \realpath(__DIR__ . '/../../config/roadrunner.php'),
            \realpath($path = ServiceProvider::getConfigPath())
        );

        $this->assertFileExists($path);
    }

    /**
     * @return void
     */
    public function testRegisterConfigs(): void
    {
        $package_config_src    = \realpath(ServiceProvider::getConfigPath());
        $package_config_target = $this->app->configPath(\basename(ServiceProvider::getConfigPath()));

        $this->assertSame(
            $package_config_target,
            IlluminateServiceProvider::$publishes[ServiceProvider::class][$package_config_src]
        );

        $this->assertSame(
            $package_config_target,
            IlluminateServiceProvider::$publishGroups['config'][$package_config_src],
            "Publishing group value {$package_config_target} was not found"
        );
    }

    /**
     * @return void
     */
    public function testEventListenersBooting(): void
    {
        /** @var ConfigRepository $config */
        $config = $this->app->make(ConfigRepository::class);

        /** @var EventsDispatcher $events */
        $events = $this->app->make(EventsDispatcher::class);

        foreach ($config->get('roadrunner.listeners') as $event => $listeners) {
            if (!empty($listeners)) {
                $this->assertTrue($events->hasListeners($event), "Event [{$event}] has no listeners");
            }
        }
    }

    /**
     * @return void
     */
    public function testMiddlewareIsRegistered(): void
    {
        $this->assertTrue($this->app->make(HttpKernel::class)->hasMiddleware(Middleware::class));
    }

    /**
     * @return void
     */
    public function testDumperRegistration(): void
    {
        $this->assertInstanceOf(FixedArrayStack::class, $this->app->make(StackInterface::class));
        $this->assertInstanceOf(Dumper::class, $this->app->make(Dumper::class));
    }
}
