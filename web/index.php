<?php

if (php_sapi_name() === 'cli-server' && is_file(__DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']))) {
    return false;
}

$app = require __DIR__.'/../src/app.php';
$app->run();
