<?php

namespace Acme\DemoBundle\DataFixtures\PHPCR;

use Nelmio\Alice\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Cmf\Bundle\SimpleCmsBundle\Doctrine\Phpcr\Page;

/**
 * Loads the initial demo data of the demo website.
 */
class LoadDemoData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed DocumentManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // load the yaml file in src/Acme/DemoBundle/Resources/data/pages.yml
        Fixtures::load(array(__DIR__.'/../../Resources/data/pages.yml'), $manager);

        // load the blocks
        Fixtures::load(array(__DIR__.'/../../Resources/data/blocks.yml'), $manager);

        $page = $manager->find(null, '/cms/simple');
        $page->setBody('Hello');
        $page->setDefault('_template', 'AcmeDemoBundle::home.html.twig');

        $manager->flush();
    }
}
