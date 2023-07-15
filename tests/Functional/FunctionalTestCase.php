<?php

declare(strict_types=1);

namespace YAR\Tests\Functional;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Monolog\Handler\Handler;
use Monolog\Handler\NoopHandler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use YAR\Infrastructure\DI;

abstract class FunctionalTestCase extends TestCase implements ServiceProvider
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->register(new DI\Base());
        $this->container->register(new DI\Websockets());
        $this->provide($this->container);

        $this->container->get(HttpServer::class)->start(
            $this->container->get(RequestHandler::class),
            $this->container->get(ErrorHandler::class)
        );
    }

    protected function tearDown(): void
    {
        $this->container->get(HttpServer::class)->stop();
    }

    public function provide(Container $c): void
    {
        $c->set(Handler::class, static function (): Handler {
            return new NoopHandler();
        });
    }
}
