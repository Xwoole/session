<?php

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
    
    
    dump("[Test] destroying");
    $id = $identifier->get();
    $session->destroy();
    assert($identifier->get() == "", "failed to clear the id");
    assert($storage->check($id) == false, "failed to remove the associated stored data");
    
    $response->end();
});

$server->after(100, function() use ($server)
{
    $client = new Client("0.0.0.0", 8080);
    $client->get("/");
    $server->shutdown();
});

$server->start();
