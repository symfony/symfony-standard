<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @extra:Route("/")
 */
class WelcomeController extends Controller
{
    /**
     * @extra:Route("/", name="_welcome")
     * @extra:Template()
     */
    public function indexAction()
    {
        return array();
    }
}
