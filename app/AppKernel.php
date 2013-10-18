<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

use Symfony\Component\Yaml\Yaml;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = $this->getConfiguredBundles(__DIR__.'/config/enabled_bundles.yml');

        // Adding a bundle that has a dependency on the kernel:
        // $bundles[] = new Acme\KernelDependentBundle\AcmeKernelDependentBundle($this);

        return $bundles;
    }

    protected function getConfiguredBundles($configurationPath)
    {
        $configurationContent = file_get_contents($configurationPath);
        $bundlesConfig = Yaml::parse($configurationContent);

        $instanciator = function($fullyQualifiedClassname) {
            return new $fullyQualifiedClassname();
        };

        $bundles = array_map($instanciator, $bundlesConfig['unrestricted_bundles']);
        foreach ($bundlesConfig['restricted_bundles'] as $restriction) {
            if (in_array($this->getEnvironment(), $restriction['environments'])) {
                $restrictedBundles = array_map($instanciator, $restriction['bundles']);
                $bundles = array_merge($bundles, $restrictedBundles);
            }
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
