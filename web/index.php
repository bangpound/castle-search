<?php

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application(array(
    'route_class' => 'Route',
));

require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';
$app['http_cache']->run();
