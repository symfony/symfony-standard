<?php

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Hello
{
    use ControllerTrait;

    /**
     * @Route("/", name="homepage")
     */
    public function __invoke(Request $request): Response
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath(__DIR__.'/../..').DIRECTORY_SEPARATOR,
        ]);
    }
}
