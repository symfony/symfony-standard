<?php

namespace SymfonyWorkshop\FilesystemRoutingBundle\Controller\about;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class symfony extends Controller
{
    public function getAction()
    {
        return new Response('About Symfony ...', 200, array('Content-Type' => 'text/plain'));
    }
}
