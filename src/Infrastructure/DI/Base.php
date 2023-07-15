<?php

declare(strict_types=1);

namespace YAR\Infrastructure\DI;

use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Websocket\Server\WebsocketGateway;
use Monolog\Handler\Handler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use YAR\Application\PublishEvent;
use YAR\Application\Subscribe;
use YAR\Application\Unsubscribe;
use YAR\Domain\EventRepository;
use YAR\Domain\SubscriptionRepository;
use YAR\Infrastructure\Persistence\InMemoryEventRepository;
use YAR\Infrastructure\Persistence\InMemorySubscriptionRepository;

use function Amp\ByteStream\getStdout;

final class Base implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(Handler::class, static function (): Handler {
            $handler = new StreamHandler(getStdout());
            $handler->setFormatter(new ConsoleFormatter());

            return $handler;
        });

        $c->set(LoggerInterface::class, static function (ContainerInterface $c): LoggerInterface {
            $logger = new Logger('yar');
            $logger->pushHandler($c->get(Handler::class));

            return $logger;
        });

        $c->set(EventRepository::class, static function (): EventRepository {
            return new InMemoryEventRepository();
        });

        $c->set(SubscriptionRepository::class, static function (): SubscriptionRepository {
            return new InMemorySubscriptionRepository();
        });

        $c->set(PublishEvent::class, static function (ContainerInterface $c): PublishEvent {
            return new PublishEvent(
                $c->get(EventRepository::class),
                $c->get(SubscriptionRepository::class),
                $c->get(WebsocketGateway::class)
            );
        });

        $c->set(Subscribe::class, static function (ContainerInterface $c): Subscribe {
            return new Subscribe(
                $c->get(EventRepository::class),
                $c->get(SubscriptionRepository::class),
                $c->get(WebsocketGateway::class)
            );
        });
        $c->set(Unsubscribe::class, static function (ContainerInterface $c): Unsubscribe {
            return new Unsubscribe(
                $c->get(SubscriptionRepository::class)
            );
        });
    }
}
