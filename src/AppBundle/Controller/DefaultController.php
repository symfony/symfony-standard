<?php

namespace AppBundle\Controller;

use AppBundle\MyService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultController
{
    use ControllerTrait;

    private $myService;
    private $container;

    public function __construct(MyService $myService, ContainerInterface $container) // to be removed, just to show advantages of this system
    {
        $this->myService = $myService;
        $this->container = $container;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $this->myService->sayHello($request->query->get('name', 'Kévin'));

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
