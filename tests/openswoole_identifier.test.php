<?php

namespace XwooleTest;

use OpenSwoole\Coroutine\Http\Client;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
use Xwoole\Session\Identifier\OpenswooleIdentifer;

require_once __DIR__ . "/../vendor/autoload.php";

$server = new Server("0.0.0.0", 8080);

$server->on("request", function(Request $request, Response $response)
{
    $identifier = new OpenswooleIdentifer($request, $response);
    
    
    dump("[Test] getting empty id");
    assert($identifier->get() == "", "failed to get empty id");
    
    
    dump("[Test] setting id");
    $identifier->set("123456789");
    assert(str_starts_with($response->cookie[0], $identifier->cookieName."=123456789"));
    
    
    dump("[Test] getting id");
    assert($identifier->get() == "123456789", "failed to get id");
    
    
    dump("[Test] unsetting id");
    $identifier->unset();
    assert(str_starts_with($response->cookie[1], $identifier->cookieName."=deleted"));
    
    $response->end();
});

$server->after(100, function() use ($server)
{
    $client = new Client("0.0.0.0", 8080);
    $client->get("/");
    $server->shutdown();
});

$server->start();
