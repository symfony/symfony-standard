<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

use Symfony\Component\Yaml\Parser;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();

        $yaml = new Parser();
        $bundlesConfig = $yaml->parse(file_get_contents(__DIR__.'/config/enabled_bundles.yml'));
        foreach ($bundlesConfig['unrestricted_bundles'] as $bundle) {
            $bundles[] = new $bundle();
        }

        foreach ($bundlesConfig['restricted_bundles'] as $restriction) {
            if (in_array($this->getEnvironment(), $restriction['environments'])) {
                foreach ($restriction['bundles'] as $bundle) {
                    $bundles[] = new $bundle();
                }
            }
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
