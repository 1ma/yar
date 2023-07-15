<?php

declare(strict_types=1);

namespace YAR\Tests\Functional;

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Websocket\Client\WebsocketHandshake;
use Amp\Websocket\WebsocketClient;
use Monolog\Handler\NoopHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use YAR\Infrastructure\DI;

use function Amp\Websocket\Client\connect;

abstract class FunctionalTestCase extends TestCase implements ServiceProvider
{
    protected ContainerInterface $container;
    protected HttpServer $server;
    protected WebsocketClient $client;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->register(new DI\Base());
        $this->container->register(new DI\Websockets());

        $this->provide($this->container);

        $this->server = $this->container->get(HttpServer::class);
        $this->server->start(
            $this->container->get(RequestHandler::class),
            $this->container->get(ErrorHandler::class)
        );

        $this->client = connect(new WebsocketHandshake('ws://127.0.0.1:1337'));
    }

    protected function tearDown(): void
    {
        $this->client->close();
        $this->server->stop();
    }

    public function provide(Container $c): void
    {
        $c->set(LoggerInterface::class, static function (): LoggerInterface {
            $logger = new Logger('test');
            $logger->pushHandler(new NoopHandler());

            return $logger;
        });
    }
}
