<?php

declare(strict_types=1);

namespace YAR\Application;

use Amp\Websocket\Server\WebsocketGateway;
use Amp\Websocket\WebsocketClient;
use YAR\Domain\Event;
use YAR\Domain\EventRepository;
use YAR\Domain\JSON;
use YAR\Domain\SubscriptionRepository;

final class PublishEvent
{
    private readonly EventRepository $eventRepository;
    private readonly SubscriptionRepository $subscriptionRepository;
    private readonly WebsocketGateway $gateway;

    public function __construct(EventRepository $eventRepository, SubscriptionRepository $subscriptionRepository, WebsocketGateway $gateway)
    {
        $this->eventRepository = $eventRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->gateway = $gateway;
    }

    public function execute(Event $event, WebsocketClient $publisher): void
    {
        $this->eventRepository->add($event);

        $publisher->send(JSON::encode(['OK', $event->id, true, '']));

        foreach ($this->subscriptionRepository->matches($event) as $subscription) {
            $this->gateway->send(JSON::encode(['EVENT', $subscription->id, $event]), $subscription->clientId);
        }
    }
}
