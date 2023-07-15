<?php

declare(strict_types=1);

namespace YAR\Tests\Functional;

use Amp\Websocket\Client\WebsocketHandshake;
use Amp\Websocket\WebsocketClient;

use function Amp\Websocket\Client\connect;

final class SubscriberTest extends FunctionalTestCase
{
    private WebsocketClient $publisherClient;
    private WebsocketClient $subscriberClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->publisherClient = connect(new WebsocketHandshake('ws://127.0.0.1:1337'));
        $this->subscriberClient = connect(new WebsocketHandshake('ws://127.0.0.1:1337'));
    }

    protected function tearDown(): void
    {
        $this->publisherClient->close();
        $this->subscriberClient->close();
        parent::tearDown();
    }

    public function testPublishingAnEvent(): void
    {
        $this->subscriberClient->send('["REQ", "1234", {"authors": ["84fdf029f065438702b011c2002b489fd00aaea69b18efeae8261c44826a8886"]}]');

        self::assertSame(
            '["EOSE","1234"]',
            $this->subscriberClient->receive()->buffer()
        );

        $this->publisherClient->send(<<<JSON
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
            '["EVENT","1234",{"id":"62fa167369a603b1181a49ecf2e20e7189833417c3fb49666c5644901da27bcc","pubkey":"84fdf029f065438702b011c2002b489fd00aaea69b18efeae8261c44826a8886","created_at":1689033061,"kind":1,"tags":[],"content":"This event was created at https://nostrtool.com/ with a throwaway key.","sig":"a67e8d286605e3d7dfd3e0bd1642f85a25bb0cd70ec2ed941349ac879f617868a3ffa2a9040bb43c024594a79e4878429a990298c51ae4d6d20533589f4a04df"}]',
            $this->subscriberClient->receive()->buffer()
        );

        self::assertSame(
            '["OK","62fa167369a603b1181a49ecf2e20e7189833417c3fb49666c5644901da27bcc",true,""]',
            $this->publisherClient->receive()->buffer()
        );
    }
}
