<?php

declare(strict_types=1);

namespace YAR\Infrastructure\Websockets;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\Server\WebsocketGateway;
use Amp\Websocket\WebsocketClient;
use Amp\Websocket\WebsocketCloseInfo;
use Psr\Log\LoggerInterface;
use YAR\Application\PublishEvent;
use YAR\Application\Subscribe;
use YAR\Application\Unsubscribe;
use YAR\Domain\Event;
use YAR\Domain\EventTag;
use YAR\Domain\Filter;
use YAR\Domain\JSON;
use YAR\Domain\Subscription;
use YAR\Domain\SubscriptionRepository;

final class FrontController implements WebsocketClientHandler
{
    private LoggerInterface $logger;
    private SubscriptionRepository $subscriptionRepository;
    private WebsocketGateway $gateway;
    private PublishEvent $publishEvent;
    private Subscribe $subscribe;
    private Unsubscribe $unsubscribe;

    public function __construct(LoggerInterface $logger, SubscriptionRepository $subscriptionRepository, WebsocketGateway $gateway, PublishEvent $publishEvent, Subscribe $subscribe, Unsubscribe $unsubscribe)
    {
        $this->logger = $logger;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->gateway = $gateway;
        $this->publishEvent = $publishEvent;
        $this->subscribe = $subscribe;
        $this->unsubscribe = $unsubscribe;
    }

    public function handleClient(WebsocketClient $client, Request $request, Response $response): void
    {
        $client->onClose(function (int $id, WebsocketCloseInfo $info): void {
            $this->subscriptionRepository->deleteAll($id);
        });

        $this->gateway->addClient($client);

        while ($message = $client->receive()) {
            $text = $message->buffer();

            try {
                $data = JSON::decode($text);
            } catch (\JsonException) {
                $this->logger->info('Client '.$client->getId().' did not send valid json');
                continue;
            }

            if (!\is_array($data)) {
                $this->logger->info('Client '.$client->getId().' did not send json array');
                continue;
            }

            if (2 === \count($data) && 'EVENT' === $data[0]) {
                $tags = [];
                foreach ($data[1]->tags as $tag) {
                    $name = $tag[0];
                    $values = \array_slice($tag, 1);
                    $tags[] = new EventTag($name, ...$values);
                }

                $this->publishEvent->execute(new Event(
                    $data[1]->id,
                    $data[1]->pubkey,
                    $data[1]->created_at,
                    $data[1]->kind,
                    $data[1]->content,
                    $data[1]->sig,
                    ...$tags
                ), $client);
                continue;
            }

            if (\count($data) >= 3 && 'REQ' === $data[0] && \is_string($data[1])) {
                $filters = [];
                for ($i = 2; $i < \count($data); ++$i) {
                    $filters[] = new Filter(
                        $data[$i]->ids ?? null,
                        $data[$i]->authors ?? null,
                        $data[$i]->kinds ?? null,
                        $data[$i]->e ?? null,
                        $data[$i]->p ?? null,
                        $data[$i]->since ?? null,
                        $data[$i]->until ?? null,
                        $data[$i]->limit ?? null,
                    );
                }

                $this->subscribe->execute(new Subscription($data[1], $client->getId(), ...$filters));
                continue;
            }

            if (2 === \count($data) && 'CLOSE' === $data[0] && \is_string($data[1])) {
                $this->unsubscribe->execute(new Subscription($data[1], $client->getId()));
                continue;
            }

            // Anything else is a NOOP
            $this->logger->info('Client '.$client->getId().' sent unknown command', ['command' => $text]);
        }
    }
}
