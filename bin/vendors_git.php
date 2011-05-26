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

//First run
if (!is_file('.gitmodules')) {
    //Detach project and initialize new git-repo
	  echo "> First run. Detaching git-repo.\n";
    if (is_dir('.git')) {
        if (PHP_OS == 'WINNT') {
            system('rmdir /S /Q '.realpath($rootDir.'/.git'));
        } else {
            system('rm -rf .git');
        }
    }
    system('git init');
    system('git add .');
    //Check and clean .gitignore
    $file = file($rootDir.'/.gitignore');
    $newfile = array();
    foreach ($file as $line) {
      if (false !== strpos($line, 'vendor/')) {
          continue;
      }
      $newfile[] = $line;
    }
    $file = fopen($rootDir.'/.gitignore','w');
    fwrite($file, implode($newfile));
    fclose($file);
    system('git add .gitignore');
}

if (!is_dir($vendorDir)) {
    mkdir($vendorDir, 0777, true);
}

// Get versions
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
    $installDir = './vendor'.$path.'/'.$name;
    $rev = isset($versions[$name]) ? $versions[$name] : 'origin/HEAD';
    //Add submodule
    echo "> Installing/Updating $name\n";
    if (!is_dir($installDir)) {
        system(sprintf('git submodule add %s %s', $url, $installDir));
    }
    //Set submodules to given version
    system(sprintf('cd %s && git fetch origin && git reset --hard %s', $installDir, $rev));

    //Add the last mod to the commit-stage
    system(sprintf('git add %s', $installDir));
}

// php on windows can't use the shebang line from system()
$interpreter = PHP_OS == 'WINNT' ? 'php.exe' : '';

// Update the bootstrap files
system(sprintf('%s %s/bin/build_bootstrap.php', $interpreter, $rootDir));

// Update assets
system(sprintf('%s %s/app/console assets:install %s/web/', $interpreter, $rootDir, $rootDir));

// Remove the cache
system(sprintf('%s %s/app/console cache:clear --no-warmup', $interpreter, $rootDir));
