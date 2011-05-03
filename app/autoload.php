<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$autoloadInfo = parse_ini_file(__DIR__.'/config/autoload.ini', true);

if (isset($autoloadInfo['namespaces'])) {
    foreach ($autoloadInfo['namespaces'] as $namespace => $location) {
        $loader->registerNamespace($namespace, convertToAbsolutePath($location));
    }
}

if (isset($autoloadInfo['prefixes'])) {
    foreach ($autoloadInfo['prefixes'] as $prefix => $location) {
        $loader->registerPrefix($prefix, convertToAbsolutePath($location));
    }
}

if (isset($autoloadInfo['prefixfallbacks'])) {
    foreach ($autoloadInfo['prefixfallbacks'] as $prefix => $location) {
        $loader->registerPrefixFallback($prefix, convertToAbsolutePath($location));
    }
}

$loader->register();

function convertToAbsolutePath($path)
{
    if (is_array($path)) {
        return array_map('convertToAbsolutePath', $path);
    }
    
    return __DIR__.'/../'.$path;
}
