<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class SelfRemoveCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('demo:self-remove')
            ->setDescription('Remove the Acme vendor and DemoBundle from default Symfony Standard distribution')
            ->setHelp(
            <<<EOF
The <info>%command.name%</info> <error>removes</error> the AcmeBundle shipped with Symfony2 Standard.

Since this operation is not reversible you will be asked if you are sure that
you want to delete the bundle.

If you choose <info>N</info> then the system will run this in dry-run mode.
If you choose <info>a</info> then the system will abort this command.
If you choose <info>y</info> then the system will proceed and remove the AcmeDemoBundle
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Check if we have the confirmation from the user in order to actually delete this
        $dialog = $this->getHelperSet()->get('dialog');

        $routeRemovalWarning = 'Confirm removal of AcmeDemoBundle? [ayN] ';
        $confirm = $dialog->ask($output, $routeRemovalWarning, 'n');

        switch (strtolower($confirm)) {
            case 'a':
                $output->writeln("Aborting operation");

                return;
            case 'y':
                $confirm = true;
                break;
            default:
                $confirm = false;
        }

        // Get the path to our Vendor/Bundle directory
        $path = realpath(__DIR__ . '/../../');

        if ($confirm) {
            $message = 'Removing: ';
        } else {
            $message = 'Dry-run: ';
        }

        $output->writeln(sprintf("%s Removing AcmeDemoBundle directory (%s)", $message, $path));

        if ($confirm) {
            $fs = new Filesystem();

            try {
                $fs->remove($path);
            } catch (IOException $e) {
                $output->writeln("<error>An error occured while removing the AcmeDemoBundle directory</error>");
            }
        }

        // Next step is to remove the routes to the bundle

        // Get the Kernel dir path
        $kernelDirPath = $this->getContainer()->get('kernel')->getRootDir();

        // Change the routes file
        $routesFile = $kernelDirPath . '/config/routing_dev.yml';
        $output->writeln('Changing the contents of the routes file ... ' . $routesFile);

        // Remove just the routes that are related to Acme/DemoBundle
        if ($confirm) {
            $routesContents = file($routesFile);
            $routesCount = count($routesContents);

            $foundMatch = false;
            $i = 0;
            while ($i < $routesCount) {
                if ($routesContents[$i] == "_acme_demo:\n") {
                    $foundMatch = true;
                    unset($routesContents[$i]);
                    $i++;
                    continue;
                }

                if ($foundMatch) {
                    if (strpos($routesContents[$i], "\t") !== false ||
                        strpos($routesContents[$i], " ") !== false
                    ) {
                        unset($routesContents[$i]);
                    } else {
                        break;
                    }
                }

                $i++;
            }

            // Restore the file contents
            $routesContents = implode('', $routesContents);
            file_put_contents($routesFile, $routesContents);
        }

        // The final step is to change the AppKernel Contents

        // Get the kernel file
        $kernelFile = $kernelDirPath . '/AppKernel.php';
        $output->writeln('Changing the contents of AppKernel ... ' . $kernelFile);

        // Get the contents
        $kernelContents = file_get_contents($kernelFile);

        // Remove the AcmeDemoBundle
        $kernelContents = preg_replace(
            '/\s+\\$bundles\[\]\s+=\s+new Acme\\\\DemoBundle\\\\AcmeDemoBundle\(\);/m',
            '',
            $kernelContents
        );

        // Check if we can save the new kernel file
        if ($confirm) {
            file_put_contents($kernelFile, $kernelContents);
        }

        $output->writeln('');

        // Finish
        $output->writeln('Done');
    }
}
