<?php

declare(strict_types=1);

namespace YAR\Application;

use Amp\Websocket\Server\WebsocketGateway;
use YAR\Domain\EventRepository;
use YAR\Domain\JSON;
use YAR\Domain\Subscription;
use YAR\Domain\SubscriptionRepository;

final class Subscribe
{
    private readonly EventRepository $eventRepository;
    private readonly SubscriptionRepository $subscriptionRepository;
    private readonly WebsocketGateway $gateway;

    public function __construct(EventRepository $eventRepository, SubscriptionRepository $subscriptionRepository, WebsocketGateway $gateway)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->eventRepository = $eventRepository;
        $this->gateway = $gateway;
    }

    public function execute(Subscription $subscription): void
    {
        foreach ($this->eventRepository->query($subscription) as $event) {
            $this->gateway->send(JSON::encode(['EVENT', $subscription->id, $event]), $subscription->clientId);
        }

        $this->gateway->send(JSON::encode(['EOSE', $subscription->id]), $subscription->clientId);

        $this->subscriptionRepository->register($subscription);
    }
}
