<?php

declare(strict_types=1);

namespace YAR\Infrastructure\DI;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Driver\SocketClientFactory;
use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\SocketHttpServer;
use Amp\Socket\InternetAddress;
use Amp\Socket\ResourceServerSocketFactory;
use Amp\Websocket\Server\Rfc6455Acceptor;
use Amp\Websocket\Server\Websocket;
use Amp\Websocket\Server\WebsocketClientGateway;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\Server\WebsocketGateway;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use YAR\Application\PublishEvent;
use YAR\Application\Subscribe;
use YAR\Application\Unsubscribe;
use YAR\Domain\SubscriptionRepository;
use YAR\Infrastructure\Websockets\FrontController;

final class Websockets implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(HttpServer::class, static function (ContainerInterface $c): HttpServer {
            $server = new SocketHttpServer(
                $c->get(LoggerInterface::class),
                new ResourceServerSocketFactory(),
                new SocketClientFactory($c->get(LoggerInterface::class))
            );

            $server->expose(new InternetAddress('127.0.0.1', 1337));

            return $server;
        });

        $c->set(WebsocketGateway::class, static function (): WebsocketGateway {
            return new WebsocketClientGateway();
        });

        $c->set(WebsocketClientHandler::class, static function (ContainerInterface $c): WebsocketClientHandler {
            return new FrontController(
                $c->get(LoggerInterface::class),
                $c->get(SubscriptionRepository::class),
                $c->get(WebsocketGateway::class),
                $c->get(PublishEvent::class),
                $c->get(Subscribe::class),
                $c->get(Unsubscribe::class)
            );
        });

        $c->set(RequestHandler::class, static function (ContainerInterface $c): RequestHandler {
            return new Websocket(
                $c->get(HttpServer::class),
                $c->get(LoggerInterface::class),
                new Rfc6455Acceptor(),
                $c->get(WebsocketClientHandler::class)
            );
        });

        $c->set(ErrorHandler::class, static function (): ErrorHandler {
            return new DefaultErrorHandler();
        });
    }
}
