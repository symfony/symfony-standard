<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', array(
            'base_dir' => str_replace('/', DIRECTORY_SEPARATOR, realpath($this->getParameter('kernel.root_dir').'/../').'/'),
        ));
    }
}
