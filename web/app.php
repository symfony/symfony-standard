<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$config_file = __DIR__.'/../app/cache/app_config.php';

if (!file_exists($config_file)) {
    // Generate default parameters
    try {
        require __DIR__.'/../app/generate_app.php';
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error');
        die($e->getMessage());
    }
}

// Load default parameters
// If this file is included with $parameters defined, they will be used instead
require $config_file;

if (isset($parameters)) {
    $parameters = array_merge($default_parameters, $parameters);
} else {
    $parameters = $default_parameters;
}

if (!isset($parameters['debug'])) {
    // Change this line if you want to be in debug for environment other than dev
    $parameters['debug'] = $parameters['environment'] == 'dev';
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

if ($parameters['environment'] == 'dev' && $parameters['localhost_only_dev']) {
    // This check prevents access to debug front controllers that are deployed by accident to production servers.
    // Feel free to remove this, extend it, or make something more sophisticated.

    if (isset($_SERVER['HTTP_CLIENT_IP'])
        || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
    ) {
        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file.');
    }

    Debug::enable();
}

if ($parameters['umask_fix']) {
    // If you don't want to setup permissions the proper way, just uncomment the following PHP line
    // read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information

    umask(0000);
}

if ($parameters['environment'] == 'prod' && !empty($parameters['apc_cache_id'])) {
    // if $apc_cache_id is specified, use APC for autoloading to improve performance.
    // $apc_cache_id needs to be a unique prefix in order to prevent cache key conflicts with other applications also using APC.

    $loader = new ApcClassLoader($parameters['apc_cache_id'], $loader);
    $loader->register(true);
}

require_once __DIR__ . '/../app/AppKernel.php';
$kernel = new AppKernel($parameters['environment'], $parameters['debug']);
$kernel->loadClassCache();

if ($parameters['environment'] == 'prod' && $parameters['http_cache']) {
    // Activates Symfony's reverse proxy
    require_once __DIR__ . '/../app/AppCache.php';
    $kernel = new AppCache($kernel);
}

Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
