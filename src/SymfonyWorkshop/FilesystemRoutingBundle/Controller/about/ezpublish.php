<?php

namespace SymfonyWorkshop\FilesystemRoutingBundle\Controller\about;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ezpublish extends Controller
{
    public function getAction()
    {
        return new Response('About eZ Publish ...', 200, array('Content-Type' => 'text/plain'));
    }
}
