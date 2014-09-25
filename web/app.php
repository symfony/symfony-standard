<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

// This check prevents explicit access to front controller, except while Apache
// mod_rewrite fallback
// Feel free to remove this, extend it, or make something more sophisticated.
if (preg_match('#^/(.[^/]+)#', $_SERVER['REQUEST_URI'], $matches)
    && $_SERVER['SCRIPT_NAME'] === $matches[0]
) {
    if (false === stripos($_SERVER['SERVER_SOFTWARE'], 'apache')
        || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()))
    ) {
        header('HTTP/1.0 404 Not found');
        exit();
    }
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
