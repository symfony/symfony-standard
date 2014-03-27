<?php

namespace Acme\DemoBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    static protected $fixturesLoaded = false;

    public function setUp()
    {
        if (self::$fixturesLoaded) {
            return;
        }

        $this->loadFixtures(array(
            'Acme\DemoBundle\DataFixtures\PHPCR\LoadDemoData',
        ), null, 'doctrine_phpcr');

        self::$fixturesLoaded = true;
    }
}
