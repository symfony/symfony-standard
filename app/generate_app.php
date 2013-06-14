<?php

/**
 * Since Symfony 2.3, parameters.yml is ignored and generated via parameters.yml.dist
 * This file generates an app.php based on the original app.php and app_dev.php, depending on a parameter from parameters.yml
 *
 * Used parameters are:
 *
 * environment: (dev|test|prod)
 * http_cache: (true|false) whether or not to use Symfony HTTP reverse proxy
 * localhost_only_dev: (true|false) whether or not to limit request to localhost when in dev mode
 * apc_cache_id: (false|string) if a valid string, use this as an APC id for the caches
 *
 * If either of those variables are changed, you will have to remove app/cache/app.php
 */

use Symfony\Component\Yaml\Yaml;

require_once __DIR__.'/bootstrap.php.cache';

$app_file        = __DIR__ . '/cache/app.php';
$parameters_file = __DIR__ . '/config/parameters.yml';

if (!file_exists($parameters_file)) {
    throw new RuntimeRuntimeException("Unable to read parameters.yml");
}

$parameters         = Yaml::parse($parameters_file);

// Extract parameters
$parameters         = $parameters['parameters'];
$environment        = $parameters['environment'];
$debug              = $environment != 'prod';
$debug_str          = $debug ? 'true' : 'false';
$localhost_only_dev = $parameters['environment'] != 'prod' && !empty($parameters['localhost_only_dev']);
$apc_cache_id       = $parameters['environment'] == 'prod' && !empty($parameters['apc_cache_id']) ? $parameters['apc_cache_id'] : false;
$http_cache         = $parameters['environment'] == 'prod' && !empty($parameters['http_cache']);

if (php_sapi_name() == 'cli') {
    echo "Generating app/cache/app.php for the {$parameters['environment']} environment\n";
}

$app_content = "<?php\n# This file was generated in " . __FILE__ . "\n";

if ($localhost_only_dev) $app_content .= <<<PHP

    if (isset(\$_SERVER['HTTP_CLIENT_IP'])
        || isset(\$_SERVER['HTTP_X_FORWARDED_FOR'])
        || !in_array(@\$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
    ) {
        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file.');
    }

PHP;

if ($debug) $app_content .= <<<PHP

    \Symfony\Component\Debug\Debug::enable();

PHP;

// if $apc_cache_id is specified, use APC for autoloading to improve performance.
// $apc_cache_id needs to be a unique prefix in order to prevent cache key conflicts with other applications also using APC.
if ($apc_cache_id) $app_content .= <<<PHP

    use Symfony\Component\ClassLoader\ApcClassLoader;

    \$loader = new ApcClassLoader('$apc_cache_id', \$loader);
    \$loader->register(true);

PHP;

// Common code
$app_content .= <<<PHP

    require_once __DIR__ . '/../AppKernel.php';
    \$kernel = new AppKernel('$environment', $debug_str);
    \$kernel->loadClassCache();

PHP;

if ($http_cache) $app_content .= <<<PHP

    require_once __DIR__ . '/../AppCache.php';
    \$kernel = new AppCache(\$kernel);

PHP;

if (!is_dir(dirname($app_file)) && !mkdir(dirname($app_file), 0777, true)) {
    throw new RuntimeException("Unable to generate app/cache/app.php");
}

if (!file_put_contents($app_file, $app_content)) {
    throw new RuntimeException("Unable to generate app/cache/app.php");
}
