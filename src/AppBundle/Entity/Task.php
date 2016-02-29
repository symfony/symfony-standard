<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sfsqlm_task")
 */
class Task {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
    protected $task;

	/**
	 * @ORM\Column(type="datetime")
	 */
    protected $dueDate;

    public function getTask() {
        return $this->task;
    }

    public function setTask($task) {
        $this->task = $task;
    }

    public function getDueDate() {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate = null) {
        $this->dueDate = $dueDate;
    }
}