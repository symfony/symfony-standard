<?php

namespace AppBundle\Controller;

use AppBundle\MyService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $myService;

    public function __construct(MyService $myService) // to be removed, just to show advantages of this system
    {
        $this->myService = $myService;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $this->myService->sayHello($request->query->get('name', 'Kévin'));

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
