<?php

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Form\ParentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         *
         */
        $form = $this->createForm(new ParentFormType());

        return $this->render('AcmeDemoBundle:Welcome:index.html.twig',
            array('files' => $form->createView())
        );
    }
}
