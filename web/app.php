<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

// Before enabling this also add the following line to your composer.json post install/update cmd section:
// "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap"
//$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
$loader = require_once __DIR__.'/../app/autoload.php';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
//$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
