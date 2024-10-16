<?php

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
use Xwoole\Session\Identifier\OpenswooleIdentifer;
use Xwoole\Session\Session;
use Xwoole\Session\Stockist\RedisStockist;

require_once __DIR__ . "/../../vendor/autoload.php";

$server = new Server("0.0.0.0", 8080);

$server->on("request", function(Request $request, Response $response)
{
    $stockist = new RedisStockist(new Redis());
    $identifier = new OpenswooleIdentifer($request, $response);
    $session = new Session($stockist, $identifier);
    
    // $session->regenrate(); // regenerate new id
    // $session->create(); // create session
    // $session->resume(); // resume session
    // $session->reset(); // reset original values
    // $session->commit(); // save changes
    // $session->close(); // close the session (without any saves)
    // $session->finish(); // commit and close
    // $session->abort(); // reset and close
    // $session->destroy(); // remove the session
    // $session->start(); // create or resume
});

$server->start();
