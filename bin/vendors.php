#!/usr/bin/env php
<?php

/*
 * This file is part of the Symfony Standard Edition.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$rootDir = dirname(__DIR__);
$vendorDir = $rootDir.'/vendor';
$version = trim(file_get_contents($rootDir.'/VERSION'));

// Initialization
$cloneOptions = '';
if (in_array('--min', $argv)) {
    $cloneOptions = '--depth 1';
}

if (!is_dir($vendorDir)) {
    mkdir($vendorDir, 0777, true);
}

// versions
$versions = array();
foreach (file(__DIR__.'/'.$version.'.deps') as $line) {
    if (!trim($line)) {
        continue;
    }
    $parts = array_values(array_filter(explode(' ', trim($line))));
    if (2 !== count($parts)) {
        die(sprintf('The deps version file is not valid (near "%s")', $line));
    }
    $versions[$parts[0]] = $parts[1];
}

foreach (file(__DIR__.'/deps') as $line) {
    if (!trim($line)) {
        continue;
    }
    $parts = array_values(array_filter(explode(' ', trim($line))));
    if (3 !== count($parts)) {
        die(sprintf('The deps file is not valid (near "%s")', $line));
    }
    list($path, $name, $url) = $parts;

    $installDir = $vendorDir.'/'.$path.'/'.$name;
    if (in_array('--reinstall', $argv)) {
		    system('rm -rf '.$installDir);
		}
    $rev = isset($versions[$name]) ? $versions[$name] : 'origin/HEAD';

    echo "> Installing/Updating $name\n";

    if (!is_dir($installDir)) {
        system(sprintf('git clone %s %s %s', $cloneOptions, $url, $installDir));
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', $installDir, $rev));
}

// Update the bootstrap files
system($rootDir.'/bin/build_bootstrap.php');

// Update assets
system($rootDir.'/app/console assets:install '.$rootDir.'/web');

// Remove the cache
system($rootDir.'/app/console cache:clear --no-warmup');
