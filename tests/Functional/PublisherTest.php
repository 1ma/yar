<?php

declare(strict_types=1);

namespace YAR\Tests\Functional;

use Amp\Websocket\Client\WebsocketHandshake;
use YAR\Domain\EventRepository;
use YAR\Infrastructure\Persistence\InMemoryEventRepository;

use function Amp\Websocket\Client\connect;

final class PublisherTest extends FunctionalTestCase
{
    public function testPublishingAnEvent(): void
    {
        $client = connect(new WebsocketHandshake('ws://127.0.0.1:1337'));

        $client->send(<<<JSON
["EVENT", {
  "id": "62fa167369a603b1181a49ecf2e20e7189833417c3fb49666c5644901da27bcc",
  "pubkey": "84fdf029f065438702b011c2002b489fd00aaea69b18efeae8261c44826a8886",
  "created_at": 1689033061,
  "kind": 1,
  "tags": [],
  "content": "This event was created at https://nostrtool.com/ with a throwaway key.",
  "sig": "a67e8d286605e3d7dfd3e0bd1642f85a25bb0cd70ec2ed941349ac879f617868a3ffa2a9040bb43c024594a79e4878429a990298c51ae4d6d20533589f4a04df"
}]
JSON
        );

        self::assertSame(
            '["OK","62fa167369a603b1181a49ecf2e20e7189833417c3fb49666c5644901da27bcc",true,""]',
            $client->receive()->buffer()
        );

        /** @var InMemoryEventRepository $eventRepository */
        $eventRepository = $this->container->get(EventRepository::class);

        self::assertSame(1, $eventRepository->count());

        $client->close();
    }
}
