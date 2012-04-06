<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = @include __DIR__.'/../vendor/.composer/autoload.php';

if (!$loader) {
    $nl = PHP_SAPI === 'cli' ? PHP_EOL : '<br />';
    
    $finalOutput = $nl.$nl.'You must set up the project dependencies.'.$nl.
        '%s'.
        'Run the following commands in '.$binPath.':'.$nl.$nl.
        '%s'.$nl.
        'php composer.phar install'.$nl;
    $installOutput = '';
    $commandOutput = 'curl -s http://getcomposer.org/installer | php';

    $installer = @file_get_contents('http://getcomposer.org/installer');
    $binPath = __DIR__.'/../bin';
    if (is_writable($binPath) && false !== $installer) {
        $installerPath = $binPath.'/install-composer.php';
        file_put_contents($installerPath, $installer);
        $installOutput = 'The composer installer has been downloaded in '.$installerPath.$nl;
        $commandOutput = 'php install-composer.php';
    }
    die(sprintf($finalOutput, $installOutput, $commandOutput));
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
