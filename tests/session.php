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
    
    dump("[Test] starting new session");
    $session->start();
    assert($identifier->get() != "", "failed to generate an id");
    assert($storage->check($identifier->get()), "failed to store the session");
    
    
    dump("[Test] getting unavailable key");
    try
    {
        $session->get("key");
        throw new AssertionError("failed to get invalid key value");
    }
    catch( OutOfBoundsException )
    {
        // 
    }
    
    
    dump("[Test] setting key-value");
    $session->set("key", "value");
    assert($session->get("key") == "value", "failed to get the set value");
    
    
    dump("[Test] committing");
    $session->commit();
    $data = $storage->get($identifier->get());
    assert(key_exists("key", $data), "failed to set key");
    assert($data["key"] == "value", "failed to set key-value");
    
    
    dump("[Test] unsetting key-value");
    $session->unset("key");
    try
    {
        $session->get("key");
        throw new AssertionError("failed to unset key");
    }
    catch( OutOfBoundsException )
    {
        // 
    }
    
    
    dump("[Test] resetting original values");
    $session->set("key1", "value1");
    $session->reset();
    try
    {
        $session->get("key1");
        throw new AssertionError("failed to resetting original values");
    }
    catch( OutOfBoundsException )
    {
        // 
    }
    
    
    dump("[Test] regenerating id");
    $oldId = $identifier->get();
    $session->regenerate();
    assert($identifier->get() != $oldId, "failed to change identifer");
    assert($storage->check($oldId) == false, "failed to remove old id from storage");
    assert($storage->check($identifier->get()), "failed to confirm the new id in storage");
    
    
    dump("[Test] freeing");
    $session->free();
    $data = $storage->get($identifier->get());
    assert($data == [], "failed to free");
    
    
    dump("[Test] closing");
    $session->set("key1", "value1");
    $session->close();
    $data = $storage->get($identifier->get());
    assert($session->isClosed(), "failed to close");
    assert(key_exists("key1", $data), "failed to commit before closing");
    assert($data["key1"] == "value1", "failed to commit before closing");
    
    
    $response->end();
});

$server->after(100, function() use ($server)
{
    $client = new Client("0.0.0.0", 8080);
    $client->get("/");
    $server->shutdown();
});

$server->start();
