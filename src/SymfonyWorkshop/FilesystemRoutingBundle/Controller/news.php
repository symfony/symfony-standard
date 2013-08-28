<?php

namespace SymfonyWorkshop\FilesystemRoutingBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class news extends Controller
{
    public function getAction()
    {
        return new Response('News ...', 200, array('Content-Type' => 'text/plain'));
    }
}
