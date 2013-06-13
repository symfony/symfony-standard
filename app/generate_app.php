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
 * umask_fix: (true|false) whether or not to call umask(0000)
 * apc_cache_id: (false|string) if a valid string, use this as an APC id for the caches
 *
 * If either of those variables are changed, you will have to remove app/cache/app.php
 */

use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/autoload.php';

$root_path       = dirname(__DIR__);
$config_file     = 'app/cache/app_config.php';
$config_path     = "$root_path/$config_file";
$parameters_file = 'app/config/parameters.yml';
$parameters_path = "$root_path/$parameters_file";

if (php_sapi_name() == 'cli') {
    echo "Generating $config_file\n";
}

if (!file_exists($parameters_path)) {
    throw new RuntimeRuntimeException("Unable to read $parameters_file");
}

$parameters         = Yaml::parse($parameters_path);

$exported = array(
    'environment',
    'http_cache',
    'localhost_only_dev',
    'umask_fix',
    'apc_cache_id',
);

$config_content = "<?php\n# This file was generated in " . __FILE__ . "\n\$default_parameters = array(\n";

foreach ($exported as $key) {
    if (!array_key_exists($key, $parameters['parameters'])) {
        throw new DomainException("Parameter $key missing, please refer to $parameters_file.dist");
    }

    $php_value = $parameters['parameters'][$key];

    // Value suitable for export
    if (is_bool($php_value)) {
        $php_value = $php_value ? 'true' : 'false';
    } else {
        $php_value = "'$php_value'";
    }

    $config_content .= "    '$key' => $php_value,\n";
}

$config_content .= ");\n";

if (!is_dir(dirname($config_path)) && !mkdir(dirname($config_path), 0777, true)) {
    throw new RuntimeException("Unable to generate $config_file");
}

if (!file_put_contents($config_path, $config_content)) {
    throw new RuntimeException("Unable to generate $config_file");
}
