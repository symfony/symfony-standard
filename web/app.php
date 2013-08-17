<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$apcLoader = new ApcClassLoader('sf2', $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

$isOnProductionServer = isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
;

if (!$isOnProductionServer) {
    Debug::enable();

    $stack = new Stack\Builder();
    $stack->push('Stack\UrlMap', array('/dev' => new AppKernel('dev', true)));
    $stack->push('Stack\UrlMap', array('/test' => new AppKernel('test', true)));

    $app = $stack->resolve($app);
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
