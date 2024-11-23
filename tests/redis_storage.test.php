<?php

namespace XwooleTest;

use AssertionError;
use OutOfBoundsException;
use Redis;
use Xwoole\Session\Storage\RedisStorage;

require_once __DIR__ . "/../vendor/autoload.php";


dump("[Check] connecting to the database");
$dbc = new Redis;
$dbc->flushDB();
$storage = new RedisStorage($dbc, "localhost");


dump("[Test] checking invalid id");
assert($storage->check("123456789") == false, "failed to check an invalid id");


dump("[Test] getting data using invalid id");
try
{
    $storage->get("key");
    new AssertionError("failed to get data using invalid id");
}
catch( OutOfBoundsException )
{
    
}


dump("[Test] setting an entry");
$data = serialize(["uid" => 372]);
$storage->set("123456789", $data);
assert($dbc->exists("123456789"));


dump("[Test] checking for an valid id");
assert($storage->check("123456789"), "failed to check an valid id");


dump("[Test] getting data");
assert($storage->get("123456789") == $data, "failed to get data");


dump("[Test] renaming id");
$storage->rename("123456789", "987654321");
assert($dbc->exists("987654321"), "failed to rename key, old key still exists");
assert($dbc->exists("123456789") == false, "failed to rename key, new key doesn't exists");


dump("[Test] unsetting");
$storage->unset("987654321");
assert($dbc->exists("987654321") == false, "failed to unset id");


dump("[Test] closing");
$storage->close();
assert($dbc->isConnected() == false, "fialed to close");
