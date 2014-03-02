<?php

namespace Acme\DemoBundle\DataFixtures\PHPCR;

use Nelmio\Alice\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Loads the initial pages of the website.
 */
class LoadPageData implements FixtureInterface
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
    }
}
