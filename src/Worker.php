<?php

declare(strict_types=1);

namespace pushrbx\LumenRoadRunner;

use Throwable;
use Laravel\Lumen\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Spiral\RoadRunner\Http\PSR7Worker;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Laravel\Lumen\Application as ApplicationContract;

/**
 * Idea is taken from the package: https://github.com/swooletw/laravel-swoole.
 */
class Worker implements WorkerInterface
{
    /**
     * Lumen application factory.
     */
    protected Application\FactoryInterface $app_factory;

    /**
     * PSR-7 Request/Response --> Symfony Request/Response.
     */
    protected \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface $http_factory_symfony;

    /**
     * Symfony Request/Response --> PSR-7 Request/Response.
     */
    protected \Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface $http_factory_psr7;

    /**
     * PSR-7 request factory.
     */
    protected \Psr\Http\Message\ServerRequestFactoryInterface $request_factory;

    /**
     * PSR-7 stream factory.
     */
    protected \Psr\Http\Message\StreamFactoryInterface $stream_factory;

    /**
     * PSR-7 uploads factory.
     */
    protected \Psr\Http\Message\UploadedFileFactoryInterface $uploads_factory;

    /**
     * Worker constructor.
     */
    public function __construct()
    {
        $this->app_factory          = new Application\Factory();
        $this->http_factory_symfony = new \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory();

        $psr17_factory         = new \Nyholm\Psr7\Factory\Psr17Factory();
        $this->request_factory = $this->stream_factory = $this->uploads_factory = $psr17_factory;

        $this->http_factory_psr7 = new \Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory(
            $this->request_factory,
            $this->stream_factory,
            $this->uploads_factory,
            $psr17_factory
        );
    }

    /**
     * {@inheritdoc}
     */
    public function start(WorkerOptionsInterface $options): void
    {
        $psr7_worker = new \Spiral\RoadRunner\Http\PSR7Worker(
            new \Spiral\RoadRunner\Worker(\Spiral\Goridge\Relay::create($options->getRelayDsn())),
            $this->request_factory,
            $this->stream_factory,
            $this->uploads_factory
        );

        $app = $this->createApplication($options, $psr7_worker);

        $this->fireEvent($app, new Events\BeforeLoopStartedEvent($app));

        while ($req = $psr7_worker->waitRequest()) {
            if (!($req instanceof ServerRequestInterface)) { // termination request received
                break;
            }

            $responded = false;

            if ($options->getRefreshApp()) {
                $sandbox = $this->createApplication($options, $psr7_worker);
            } else {
                $sandbox = clone $app;
            }

            $this->setApplicationInstance($sandbox);

            /** @var ConfigRepository $config */
            $config = $sandbox->make(ConfigRepository::class);

            try {
                $this->fireEvent($sandbox, new Events\BeforeLoopIterationEvent($sandbox, $req));
                $request = Request::createFromBase($this->http_factory_symfony->createRequest($req));

                $this->fireEvent($sandbox, new Events\BeforeRequestHandlingEvent($sandbox, $request));
                $sandbox->instance('request', $request);
                $response = $sandbox->handle($request);
                $this->fireEvent($sandbox, new Events\AfterRequestHandlingEvent($sandbox, $request, $response));

                $psr7_worker->respond($this->http_factory_psr7->createResponse($response));
                $responded = true;
                $sandbox->terminate();

                $this->fireEvent($sandbox, new Events\AfterLoopIterationEvent($sandbox, $request, $response));
            } catch (Throwable $e) {
                if ($responded !== true) {
                    $psr7_worker->getWorker()->error($this->exceptionToString($e, $this->isDebugModeEnabled($config)));
                }

                $this->fireEvent($sandbox, new Events\LoopErrorOccurredEvent($sandbox, $req, $e));
            } finally {
                unset($response, $request, $sandbox);

                $this->setApplicationInstance($app);
            }
        }

        $this->fireEvent($app, new Events\AfterLoopStoppedEvent($app));
    }

    /**
     * Create an Laravel application instance and bind all required instances.
     *
     * @param WorkerOptionsInterface $options
     * @param PSR7Worker             $psr7_worker
     *
     * @return ApplicationContract
     *
     * @throws Throwable
     */
    protected function createApplication(WorkerOptionsInterface $options, PSR7Worker $psr7_worker): ApplicationContract
    {
        $app = $this->app_factory->create($options->getAppBasePath());

        // Put PSR7 client into container
        $app->instance(PSR7Worker::class, $psr7_worker);

        return $app;
    }

    /**
     * @param Throwable $e
     * @param bool      $is_debug
     *
     * @return string
     */
    protected function exceptionToString(Throwable $e, bool $is_debug): string
    {
        return $is_debug
            ? (string) $e
            : 'Internal server error';
    }

    /**
     * @param ConfigRepository $config
     *
     * @return bool
     */
    protected function isDebugModeEnabled(ConfigRepository $config): bool
    {
        return $config->get('app.debug', false) === true;
    }

    /**
     * Set the current application in the container.
     *
     * @param ApplicationContract $app
     *
     * @return void
     */
    protected function setApplicationInstance(ApplicationContract $app): void
    {
        $app->instance('app', $app);
        $app->instance(Container::class, $app);

        Container::setInstance($app);

        Facade::clearResolvedInstances();
        $app->withFacades();
    }

    /**
     * @param ApplicationContract $app
     * @param object $event
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function fireEvent(ApplicationContract $app, object $event): void
    {
        /** @var EventsDispatcher $events */
        $events = $app->make(EventsDispatcher::class);

        $events->dispatch($event);
    }
}
