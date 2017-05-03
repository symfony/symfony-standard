<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }
	
    /**
     * @Route("/task", name="list_tasks")
     */
	public function listTasks() {
		$em = $this->get('doctrine')->getManager();
		$tasks = $em->getRepository('AppBundle:Task')->findAll();
		
		return $this->render('task/list.html.twig', array(
			'tasks' => $tasks,
		));
	}
	
    /**
     * @Route("/task/create", name="create_task")
     */
	public function createTask(Request $request) {
		$task = new Task();

		$form = $this->createFormBuilder($task)
			->add('task', TextType::class)
			->add('dueDate', DateType::class)
			->add('save', SubmitType::class, array('label' => 'Save'))
			->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->get('doctrine')->getManager();
			$em->persist($task);
			$em->flush();
			
			return $this->redirectToRoute('list_tasks');
		}

		return $this->render('task/create.html.twig', array(
			'form' => $form->createView(),
		));
	}
}
