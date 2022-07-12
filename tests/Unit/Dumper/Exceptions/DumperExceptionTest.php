<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Dumper\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use pushrbx\LumenRoadRunner\Dumper\Stack\FixedArrayStack;
use pushrbx\LumenRoadRunner\Dumper\Exceptions\DumperException;

/**
 * @covers \pushrbx\LumenRoadRunner\Dumper\Exceptions\DumperException
 */
class DumperExceptionTest extends \pushrbx\LumenRoadRunner\Tests\AbstractTestCase
{
    public function testDefaultCode(): void
    {
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, (new DumperException())->getCode());
    }

    public function testReport(): void
    {
        (new DumperException())->report();

        $this->markTestSkipped('nothing to test');
    }

    public function testRender(): void
    {
        $cloner = new VarCloner();

        $stack = new FixedArrayStack();
        $stack->push($cloner->cloneVar('foo'));
        $stack->push($cloner->cloneVar(123));

        $e = DumperException::withStack($stack);

        $response = $e->render();

        $this->assertStringContainsString('<html', $response->getContent());
        $this->assertStringContainsString('<body', $response->getContent());
        $this->assertStringContainsString('foo', $response->getContent());
        $this->assertStringContainsString('123', $response->getContent());
        $this->assertSame($e->getCode(), $response->getStatusCode());
    }
}
