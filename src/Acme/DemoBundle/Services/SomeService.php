<?php

namespace Acme\DemoBundle\Services;

class SomeService
{
    private $messages = array();

    public function doSomeCoolStuff($name)
    {
        $this->messages[] = $name;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}