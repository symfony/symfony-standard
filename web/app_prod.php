<?php

use WMC\AppLoader\AppLoader;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

$app_loader = new AppLoader(__DIR__ . '/../app', $loader);
$app_loader->environment = 'prod';
$app_loader->run();
