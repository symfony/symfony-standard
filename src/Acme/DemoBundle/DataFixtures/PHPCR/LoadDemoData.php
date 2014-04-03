<?php

namespace Acme\DemoBundle\DataFixtures\PHPCR;

use Nelmio\Alice\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Cmf\Bundle\SimpleCmsBundle\Doctrine\Phpcr\Page;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

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
        // tweak homepage
        $page = $manager->find(null, '/cms/simple');
        $page->setBody('Hello');
        $page->setDefault('_template', 'AcmeDemoBundle::home.html.twig');

        // add menu item for home
        $menuRoot = $manager->find(null, '/cms/simple');
        $homeMenuNode = new MenuNode('home');
        $homeMenuNode->setLabel('Home');
        $homeMenuNode->setParent($menuRoot);
        $homeMenuNode->setContent($page);

        $manager->persist($homeMenuNode);

        // load the yaml file in src/Acme/DemoBundle/Resources/data/pages.yml
        Fixtures::load(array(__DIR__.'/../../Resources/data/pages.yml'), $manager);

        // load the blocks
        Fixtures::load(array(__DIR__.'/../../Resources/data/blocks.yml'), $manager);

        // save the changes
        $manager->flush();
    }
}
