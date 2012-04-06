<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = @include __DIR__.'/../vendor/.composer/autoload.php';

if (!$loader) {
    $nl = PHP_SAPI === 'cli' ? PHP_EOL : '<br />';
    echo "$nl$nl";
    $installer = @file_get_contents('http://getcomposer.org/installer');
    $binPath = dirname(__DIR__).'/bin';
    if (is_writable($binPath) && false !== $installer) {
        echo 'You must set up the project dependencies.'.$nl;
        $installerPath = $binPath.'/install-composer.php';
        file_put_contents($installerPath, $installer);
        echo 'The composer installer has been downloaded in '.$installerPath.$nl;
        die('Run the following commands in '.$binPath.':'.$nl.$nl.
            'php install-composer.php'.$nl.
            'php composer.phar install'.$nl);
    }
    die('You must set up the project dependencies.'.$nl.
        'Run the following commands in '.$binPath.':'.$nl.$nl.
        'curl -s http://getcomposer.org/installer | php'.$nl.
        'php composer.phar install'.$nl);
}

// Import own intl implementation if module disabled
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// Swiftmailer special autoloader to allow lazy loading of exepensive init file
require_once __DIR__.'/../vendor/swiftmailer/swiftmailer/lib/classes/Swift.php';

Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/swiftmailer/lib/swift_init.php');
