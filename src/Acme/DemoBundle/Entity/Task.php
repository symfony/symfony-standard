<?php

namespace Acme\DemoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Acme\DemoBundle\Entity\Category;

class Task
{
    // ...

    /**
     * @Assert\Valid
     */
    protected $category;

    // ...

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }
}