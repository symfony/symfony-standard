<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
} elseif('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
    require __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/HttpFoundation/HeaderBag.php';
    require __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/HttpFoundation/Response.php';
    require __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/HttpFoundation/ResponseHeaderBag.php';

    $headerBag = new HeaderBag(getallheaders());
    $responseHeaders = array();

    foreach(array(
                'Access-Control-Allow-Origin' => 'Origin',
                'Access-Control-Allow-Methods' => 'Access-Control-Request-Method',
                'Access-Control-Allow-Headers' => 'Access-Control-Request-Headers',
                'Access-Control-Allow-Credentials' => 'Access-Control-Allow-Credentials'
            ) as $header => $headerKey) {
        $responseHeaders[$header] = $headerBag->get($headerKey);
    }

    $response = new Response(null, Response::HTTP_NO_CONTENT, $responseHeaders);

    $response->send();
    exit;
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
