<?php

use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

$app_file = __DIR__.'/../app/cache/app.php';

if (!file_exists($app_file)) {
    try {
        require __DIR__.'/../app/generate_app.php';
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error');
        die($e->getMessage());
    }
}

require $app_file;

Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
