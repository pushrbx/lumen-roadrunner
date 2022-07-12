<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Application;

use pushrbx\LumenRoadRunner\Application\Factory;

/**
 * @covers \pushrbx\LumenRoadRunner\Application\Factory
 */
class FactoryTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    protected const LARAVEL_BASE_PATH = __DIR__ . '/../../../vendor/laravel/laravel';

    public function testCreate(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Foundation\Application::class,
            $app = (new Factory())->create(self::LARAVEL_BASE_PATH)
        );

        $this->assertTrue($app->hasBeenBootstrapped());
    }

    public function testCreateWithoutBoostrapping(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Foundation\Application::class,
            $app = (new Factory())->create(self::LARAVEL_BASE_PATH, false)
        );

        $this->assertFalse($app->hasBeenBootstrapped());
    }

    public function testCreationFailingWithWrongPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Factory())->create('');
    }
}
