<?php

namespace Acme\DemoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    /**
     * @Assert\Length(min=3, groups={"validationgroup"})
     */
    public $name;
}