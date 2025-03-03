# Lumen framework on RoadRunner

Easy way for connecting [RoadRunner][roadrunner] and [Lumen][lumen] applications.
This is a fork of [spiral/roadrunner-laravel](https://github.com/spiral/roadrunner-laravel), which makes it work with lumen instead of the full laravel framework.

> Please note that this lib is not production ready yet, it's under development.

## Installation

Make sure that [RR binary file][roadrunner-binary-releases] already installed on your system (or docker image). Require this package with composer using next command:

```shell script
$ composer require pushrbx/lumen-roadrunner
```

> Installed `composer` is required ([how to install composer][getcomposer]).

After that you can "publish" package configuration file (`./config/roadrunner.php`) using next command:

```shell script
$ php ./artisan vendor:publish --provider='pushrbx\LumenRoadRunner\ServiceProvider' --tag=config
```

**Important**: despite the fact that worker allows you to refresh application instance on each HTTP request _(if worker started with option `--refresh-app`, eg.: `php ./vendor/bin/rr-worker start --refresh-app`)_, we strongly recommend avoiding this for performance reasons. Large applications can be hard to integrate with RoadRunner _(you must decide which of service providers must be reloaded on each request, avoid "static optimization" in some cases)_, but it's worth it.

## Usage

After package installation you can use provided "binary" file as RoadRunner worker: `./vendor/bin/rr-worker`. This worker allows you to interact with incoming requests and outgoing responses using [laravel events system][laravel_events]. Event contains:

| Event classname              | Application object | HTTP server request | HTTP request | HTTP response | Exception |
|------------------------------|:------------------:|:-------------------:|:------------:|:-------------:|:---------:|
| `BeforeLoopStartedEvent`     |         ✔          |                     |              |               |           |
| `BeforeLoopIterationEvent`   |         ✔          |          ✔          |              |               |           |
| `BeforeRequestHandlingEvent` |         ✔          |                     |      ✔       |               |           |
| `AfterRequestHandlingEvent`  |         ✔          |                     |      ✔       |       ✔       |           |
| `AfterLoopIterationEvent`    |         ✔          |                     |      ✔       |       ✔       |           |
| `AfterLoopStoppedEvent`      |         ✔          |                     |              |               |           |
| `LoopErrorOccurredEvent`     |         ✔          |          ✔          |              |               |     ✔     |

Simple `.rr.yaml` config example ([full example can be found here][roadrunner_config]):

> For `windows` path must be full (eg.: `php vendor/pushrbx/lumen-roadrunner/bin/rr-worker start`)

```yaml
version: "2.7"

server:
  command: "php ./vendor/bin/rr-worker start --relay-dsn unix:///var/run/rr-relay.sock"
  relay: "unix:///var/run/rr-relay.sock"

http:
  address: 0.0.0.0:8080
  middleware: ["static", "headers", "gzip"]
  pool:
    max_jobs: 64 # feel free to change this
    supervisor:
      exec_ttl: 60s
  headers:
    response:
      X-Powered-By: "RoadRunner"
  static:
    dir: "public"
    forbid: [".php"]
```

**Socket** or **TCP port** relay usage is strongly recommended for avoiding problems with `dd()`, `dump()`, `echo()` and other similar functions, that sends data to the IO pipes.

Roadrunner server starting:

```shell script
$ rr serve -c ./.rr.yaml
```

### Listeners

This package provides event listeners for resetting application state without full application reload _(like cookies, HTTP request, application instance, service-providers and other)_. Some of them already declared in configuration file, but you can declare own without any limitations.

### Helpers

This package provides the following helpers:

| Name            | Description                                                               |
|-----------------|---------------------------------------------------------------------------|
| `\rr\dump(...)` | Dump passed values (dumped result will be available in the HTTP response) |
| `\rr\dd(...)`   | Dump passed values and stop the execution                                 |
| `\rr\worker()`  | Easy access to the RoadRunner PSR worker instance                         |

### Known issues

#### Performance degradation

...when `file` driver is set for your sessions. Please, use `redis` (or something similar) driver instead ([related issue](https://github.com/spiral/roadrunner-laravel/issues/23)). This package or/and RoadRunner has nothing to do with it, but since this is a fairly common issue - it is described here.

#### Controller constructors

You should avoid to use HTTP controller constructors _(created or resolved instances in a constructor can be shared between different requests)_. Use dependencies resolving in a controller **methods** instead.

Bad:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * The user repository instance.
     */
    protected $users;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param UserRepository $users
     * @param Request        $request
     */
    public function __construct(UserRepository $users, Request $request)
    {
        $this->users   = $users;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function store(): Response
    {
        $user = $this->users->getById($this->request->id);

        // ...
    }
}
```

Good:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @param  Request        $request
     * @param  UserRepository $users
     *
     * @return Response
     */
    public function store(Request $request, UserRepository $users): Response
    {
        $user = $users->getById($request->id);

        // ...
    }
}
```

#### Middleware constructors

You should never to use middleware constructor for `session`, `session.store`, `auth` or auth `Guard` instances resolving and **storing** in properties _(for example)_. Use method-injection or access them through `Request` instance.

Bad:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Session\Store;

class Middleware
{
    /**
     * @var Store
     */
    protected $session;

    /**
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $name = $this->session->getName();

        // ...

        return $next($request);
    }
}
```

Good:

```php
<?php

use Illuminate\Http\Request;

class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $name = $request->session()->getName();
        // $name = resolve('session')->getName();

        // ...

        return $next($request);
    }
}
```

### Testing

For package testing we use `phpunit` framework and `docker-ce` + `docker-compose` as develop environment. So, just write into your terminal after repository cloning:

```shell script
$ make build
$ make latest # or 'make lowest'
$ make test
```

## Changes log

[![Release date][badge_release_date]][link_releases]
[![Commits since latest release][badge_commits_since_release]][link_commits]

If you find any package errors, please, [make an issue][link_create_issue] in a current repository.

## License

MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained by [pushrbx](https://github.com/pushrbx).

[badge_packagist_version]:https://img.shields.io/packagist/v/spiral/roadrunner-laravel.svg?maxAge=180
[badge_php_version]:https://img.shields.io/packagist/php-v/spiral/roadrunner-laravel.svg?longCache=true
[badge_build_status]:https://img.shields.io/github/workflow/status/spiral/roadrunner-laravel/tests?maxAge=30
[badge_chat]:https://img.shields.io/badge/discord-chat-magenta.svg
[badge_coverage]:https://img.shields.io/codecov/c/github/spiral/roadrunner-laravel/master.svg?maxAge=180
[badge_downloads_count]:https://img.shields.io/packagist/dt/spiral/roadrunner-laravel.svg?maxAge=180
[badge_license]:https://img.shields.io/packagist/l/spiral/roadrunner-laravel.svg?maxAge=256
[badge_release_date]:https://img.shields.io/github/release-date/spiral/roadrunner-laravel.svg?style=flat-square&maxAge=180
[badge_commits_since_release]:https://img.shields.io/github/commits-since/spiral/roadrunner-laravel/latest.svg?style=flat-square&maxAge=180
[badge_issues]:https://img.shields.io/github/issues/spiral/roadrunner-laravel.svg?style=flat-square&maxAge=180
[badge_pulls]:https://img.shields.io/github/issues-pr/spiral/roadrunner-laravel.svg?style=flat-square&maxAge=180
[link_releases]:https://github.com/spiral/roadrunner-laravel/releases
[link_packagist]:https://packagist.org/packages/spiral/roadrunner-laravel
[link_build_status]:https://github.com/spiral/roadrunner-laravel/actions
[link_chat]:https://discord.gg/Y3df23vJDw
[link_issues]:https://github.com/spiral/roadrunner-laravel/issues
[link_create_issue]:https://github.com/pushrbx/lumen-roadrunner/issues/new/choose
[link_commits]:https://github.com/spiral/roadrunner-laravel/commits
[link_pulls]:https://github.com/spiral/roadrunner-laravel/pulls
[link_license]:https://github.com/spiral/roadrunner-laravel/blob/master/LICENSE
[getcomposer]:https://getcomposer.org/download/
[roadrunner]:https://github.com/roadrunner-server/roadrunner
[roadrunner_config]:https://github.com/roadrunner-server/roadrunner/blob/master/.rr.yaml
[lumen]:https://lumen.laravel.com
[laravel_events]:https://laravel.com/docs/events
[roadrunner-cli]:https://github.com/spiral/roadrunner-cli
[roadrunner-binary-releases]:https://github.com/roadrunner-server/roadrunner/releases
[#10]:https://github.com/spiral/roadrunner-laravel/issues/10
