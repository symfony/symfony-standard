<?php

namespace AppBundle;

use Psr\Log\LoggerInterface;

/**
 * Just an example, to be removed.
 */
class MyService
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sayHello($name)
    {
        $this->logger->info('Hello {name}', array('name' => $name));
    }
}
