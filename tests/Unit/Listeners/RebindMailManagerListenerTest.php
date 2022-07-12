<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner\Tests\Unit\Listeners;

use Mockery as m;
use Illuminate\Mail\MailManager;
use pushrbx\LumenRoadRunner\Events\Contracts\WithApplication;
use pushrbx\LumenRoadRunner\Listeners\RebindMailManagerListener;

/**
 * @covers \pushrbx\LumenRoadRunner\Listeners\RebindMailManagerListener
 */
class RebindMailManagerListenerTest extends AbstractListenerTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testHandle(): void
    {
        $app_clone = clone $this->app;

        /* @var \Illuminate\Mail\MailManager $mail_manager */
        $mail_manager = $this->app->make('mail.manager');

        $this->setProperty($mail_manager, $app_prop = 'app', $app_clone);

        // burn 'mailers' property
        $mail_manager->driver($mail_manager->getDefaultDriver());

        /** @var m\MockInterface|WithApplication $event_mock */
        $event_mock = m::mock(WithApplication::class)
            ->makePartial()
            ->expects('application')
            ->andReturn($this->app)
            ->getMock();

        $this->assertNotEmpty($this->getProperty($mail_manager, $mailers_prop = 'mailers'));

        $this->listenerFactory()->handle($event_mock);

        $this->assertSame($this->app, $this->getProperty($mail_manager, $app_prop));
        $this->assertEmpty($this->getProperty($mail_manager, $mailers_prop));
    }

    /**
     * @return RebindMailManagerListener
     */
    protected function listenerFactory(): RebindMailManagerListener
    {
        return new RebindMailManagerListener();
    }
}
