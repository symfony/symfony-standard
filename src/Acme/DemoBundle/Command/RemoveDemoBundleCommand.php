<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class RemoveDemoBundleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('acme:bundle:remove')
            ->setDescription('Remove the Acme Demo Bundle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        $route    = Yaml::parse(file_get_contents($rootDir.'/config/routing_dev.yml'));
        $security = Yaml::parse(file_get_contents($rootDir.'/config/security.yml'));

        unset($route['_demo'], $route['_demo_secured'], $route['_welcome']);
        unset($security['security']['firewalls']['login'], $security['security']['firewalls']['secured_area']);

        file_put_contents($rootDir.'/config/routing_dev.yml', Yaml::dump($route, 6));
        file_put_contents($rootDir.'/config/security.yml', Yaml::dump($security, 6));

        $appKernel = file_get_contents($rootDir.'/AppKernel.php');
        $appKernel = str_replace('$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();', '', $appKernel);
        file_put_contents($rootDir.'/AppKernel.php', $appKernel);

        $fs = new Filesystem();
        $fs->remove([$rootDir.'/../src/Acme/']);

        $output->writeln("<info>Acme Bundle removed successfully</info>");
    }
}
