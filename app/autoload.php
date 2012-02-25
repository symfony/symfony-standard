<?php

// This class is defined on 'app/bootstrap.php.cache' file, which is created
// when running 'php bin/vendors update', and is also responsible for including
// this script as global code (non-namespaced code).
//
// When looking for the code defining this class, better check out the file at
// 'vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php',
// which is where the code comes from, and is properly documented.
//
use Symfony\Component\ClassLoader\UniversalClassLoader;

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
    'Sensio'           => __DIR__.'/../vendor/bundles',
    'JMS'              => __DIR__.'/../vendor/bundles',
    'Doctrine\\Bundle' => __DIR__.'/../vendor/bundles',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'   => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine'         => __DIR__.'/../vendor/doctrine/lib',
    'Monolog'          => __DIR__.'/../vendor/monolog/src',
    'Assetic'          => __DIR__.'/../vendor/assetic/src',
    'Metadata'         => __DIR__.'/../vendor/metadata/src',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__.'/../vendor/twig-extensions/lib',
    'Twig_'            => __DIR__.'/../vendor/twig/lib',
));

// If intl PHP extension is not available, load stub functions instead.
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallback(__DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

if (!interface_exists('SessionHandlerInterface', false)) {
    $loader->registerPrefix('SessionHandlerInterface', __DIR__.'/../vendor/symfony/src/Symfony/Component/HttpFoundation/Resources/stubs');
}

$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));

$loader->register();

// Setup Doctrine to use our $loader.
AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});

AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

// Swiftmailer needs a special autoloader to allow
// the lazy loading of the init file (which is expensive).
require_once __DIR__.'/../vendor/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/lib/swift_init.php');
