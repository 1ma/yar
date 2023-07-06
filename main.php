<?php

declare(strict_types=1);

use Amp\Http\Server\ErrorHandler;
use Amp\Http\Server\HttpServer;
use Amp\Http\Server\RequestHandler;
use UMA\DIC\Container;
use YAR\Infrastructure\DI;

use function Amp\trapSignal;

require_once __DIR__.'/vendor/autoload.php';

$c = new Container();
$c->register(new DI\Base());
$c->register(new DI\Websockets());

/** @var HttpServer $server */
$server = $c->get(HttpServer::class);
$server->start($c->get(RequestHandler::class), $c->get(ErrorHandler::class));

trapSignal([\SIGINT, \SIGTERM]);
$server->stop();
