<?php

namespace XwooleTest;

use OpenSwoole\Coroutine\Http\Client;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
use Xwoole\Session\Identifier\OpenswooleIdentifer;
use Xwoole\Session\Session;
use Xwoole\Session\Storage\FileStorage;

require_once __DIR__ . "/../vendor/autoload.php";

$server = new Server("0.0.0.0", 8080);

$server->on("request", function(Request $request, Response $response)
{
    $storage = new FileStorage(__DIR__ . "/sessions");
    $identifier = new OpenswooleIdentifer($request, $response);
    $session = new Session($storage, $identifier);
    $session->start();
    
    
    dump("[Test] aborting");
    $session["key"] = "value";
    $session->abort();
    $dictionary = unserialize($storage->get($identifier->get()));
    assert(false == $session->isActive(), "failed to close");
    assert($dictionary == [], "failed to resetting original values");
    
    $response->end();
});

$server->after(100, function() use ($server)
{
    $client = new Client("0.0.0.0", 8080);
    $client->get("/");
    $server->shutdown();
});

$server->start();
