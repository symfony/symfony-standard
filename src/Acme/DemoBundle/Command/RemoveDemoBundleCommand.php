<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

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
        $rootDir        =  $this->getContainer()->get('kernel')->getRootDir();
        $securityConfig =  $rootDir. '/config/security.yml';
        $routingConfig  = $rootDir. '/config/routing_dev.yml';
        $appKernel      = $rootDir. '/AppKernel.php';


        $config = array('routing_dev' => file_get_contents($routingConfig),
                        'security'    => file_get_contents($securityConfig));

        if ($this->hasDefaultsConfigChanged($config)) {
            $output->writeln("<error>This couldn't be performed, your config files have changed. You must remove the bundle manually</error>");
            return;
        }

        file_put_contents($securityConfig, $this->getBaseSecurityConfig());
        file_put_contents($routingConfig, $this->getBaseRoutingDevConfig());
        file_put_contents($appKernel, str_replace('$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();', '', file_get_contents($appKernel)));

        $fs = new Filesystem();
        try {
            $fs->remove(array($rootDir.'/../src/Acme/'));
        } catch (IOException $e) {
            $output->writeln("<error>An error occurred while removing the AcmeDemoBundle directory</error>");
            return;
        }

        $output->writeln("<info>Acme Bundle has been removed successfully</info>");
    }

    protected function getBaseSecurityConfig()
    {
        return
<<<EOT
jms_security_extra:
    secure_all_services: false
    expressions: true

security:
    encoders:
        Symfony\Component\Security\Core\User\User: plaintext

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

    providers:
        in_memory:
            memory:
                users:
                    user:  { password: userpass, roles: [ 'ROLE_USER' ] }
                    admin: { password: adminpass, roles: [ 'ROLE_ADMIN' ] }

    firewalls:
        dev:
            pattern:  ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            pattern:  ^/$
            anonymous: ~

    access_control:
        #- { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, requires_channel: https }
        #- { path: ^/_internal/secure, roles: IS_AUTHENTICATED_ANONYMOUSLY, ip: 127.0.0.1 }
EOT;
    }

    protected function getBaseRoutingDevConfig()
    {
        return
<<<EOT
_wdt:
    resource: "@WebProfilerBundle/Resources/config/routing/wdt.xml"
    prefix:   /_wdt

_profiler:
    resource: "@WebProfilerBundle/Resources/config/routing/profiler.xml"
    prefix:   /_profiler

_configurator:
    resource: "@SensioDistributionBundle/Resources/config/routing/webconfigurator.xml"
    prefix:   /_configurator

_main:
    resource: routing.yml
EOT;
    }

    protected function getDefaultSecurityConfig()
    {
        return
<<<EOT
jms_security_extra:
    secure_all_services: false
    expressions: true

security:
    encoders:
        Symfony\Component\Security\Core\User\User: plaintext

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

    providers:
        in_memory:
            memory:
                users:
                    user:  { password: userpass, roles: [ 'ROLE_USER' ] }
                    admin: { password: adminpass, roles: [ 'ROLE_ADMIN' ] }

    firewalls:
        dev:
            pattern:  ^/(_(profiler|wdt)|css|images|js)/
            security: false

        login:
            pattern:  ^/demo/secured/login$
            security: false

        secured_area:
            pattern:    ^/demo/secured/
            form_login:
                check_path: /demo/secured/login_check
                login_path: /demo/secured/login
            logout:
                path:   /demo/secured/logout
                target: /demo/
            #anonymous: ~
            #http_basic:
            #    realm: "Secured Demo Area"

    access_control:
        #- { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, requires_channel: https }
        #- { path: ^/_internal/secure, roles: IS_AUTHENTICATED_ANONYMOUSLY, ip: 127.0.0.1 }
EOT;
    }

    protected function getDefaultRoutingDevConfig()
    {
        return
<<<EOT
_welcome:
    pattern:  /
    defaults: { _controller: AcmeDemoBundle:Welcome:index }

_demo_secured:
    resource: "@AcmeDemoBundle/Controller/SecuredController.php"
    type:     annotation

_demo:
    resource: "@AcmeDemoBundle/Controller/DemoController.php"
    type:     annotation
    prefix:   /demo

_wdt:
    resource: "@WebProfilerBundle/Resources/config/routing/wdt.xml"
    prefix:   /_wdt

_profiler:
    resource: "@WebProfilerBundle/Resources/config/routing/profiler.xml"
    prefix:   /_profiler

_configurator:
    resource: "@SensioDistributionBundle/Resources/config/routing/webconfigurator.xml"
    prefix:   /_configurator

_main:
    resource: routing.yml
EOT;
    }

    protected function hasDefaultsConfigChanged(array $config)
    {
        if (trim($config['routing_dev']) !== $this->getDefaultRoutingDevConfig()) {
            return true;
        }
        if (trim($config['security'])  !== $this->getDefaultSecurityConfig()) {
            return true;
        }

        return false;
    }
}
