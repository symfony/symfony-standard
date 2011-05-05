<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\ClassLoader\DebugUniversalClassLoader;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundleFile = file(__DIR__.'/config/bundles.ini', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        $bundles = array();        
        $env = null;
        
        foreach ($bundleFile as $line) {
            if ($line[0] == '#') {
                continue;
            }
            
            if (preg_match('/\[([a-z]+)\]/i', $line, $match)) {
                $env = $match[1];
                continue;
            }
            
            if ($env != 'all' && $env != $this->getEnvironment()) {
                continue;
            }
            
            $bundles[] = new $line();
        }
        
        return $bundles;
    }

    public function init()
    {
        if ($this->debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);

            DebugUniversalClassLoader::enable();
            ErrorHandler::register();
        } else {
            ini_set('display_errors', 0);
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
