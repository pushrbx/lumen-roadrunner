<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use pushrbx\LumenRoadRunner\Listeners\FixSymfonyFileValidationListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\FixSymfonyFileValidationListener
 */
class FixSymfonyFileValidationListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $function_location = '\\Symfony\\Component\\HttpFoundation\\File\\is_uploaded_file';

        $this->assertFalse(\function_exists($function_location));
        $this->assertFalse(\is_uploaded_file('foo'));

        $this->listenerFactory()->handle(new \stdClass());

        $this->assertTrue(\function_exists($function_location));
        $this->assertTrue($function_location('foo'));
    }

    /**
     * @return FixSymfonyFileValidationListener
     */
    protected function listenerFactory(): FixSymfonyFileValidationListener
    {
        return new FixSymfonyFileValidationListener();
    }
}
