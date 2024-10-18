<?php

use Xwoole\Session\Storage\FileStorage;

require_once __DIR__ . "/../vendor/autoload.php";


dump("[Test] creation of sessions directory");
$dir = __DIR__ . "/sessions";
$storage = new FileStorage($dir);
assert(is_dir($dir), "failed to create sessions directory");


dump("[Test] checking file existence of invalid id");
assert($storage->check("123456789") == false, "failed to check file existence of invalid id");


dump("[Test] getting data using invalid id");
assert(file_exists("$dir/123456789") == false, "failed to unset file");


dump("[Test] creating file");
$data = ["key", "value"];
$storage->set("123456789", $data);
assert(is_file("$dir/123456789"), "failed to create file");


dump("[Test] checking for file existence");
assert($storage->check("123456789"), "failed to check for file existence");


dump("[Test] getting data");
assert($storage->get("123456789") == $data, "failed to set");


dump("[Test] renaming");
$storage->rename("123456789", "987654321");
assert($storage->check("987654321"), "failed to rename file");
assert($storage->check("123456789") == false, "failed to rename file");


dump("[Test] unsetting");
$storage->unset("987654321");
assert(file_exists("$dir/987654321") == false, "failed to unset file");
