<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\DemoBundle\Form\TaskType;
use Acme\DemoBundle\Entity\Task;

class TestController extends Controller
{
    public function indexAction()
    {
        $form = $this->createForm(new TaskType(), new Task()
            , array('validation_groups' => array('validationgroup'),)
        );
        $form->add('save', 'submit');
        
        $form->handleRequest($this->getRequest());
        
        if($form->isValid()){
            echo '<p>the form is valid...</p>';
        }
        
        return $this->render('AcmeDemoBundle:Test:taskform.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
