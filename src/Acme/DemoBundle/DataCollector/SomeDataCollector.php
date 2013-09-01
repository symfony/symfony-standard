<?php

namespace Acme\DemoBundle\DataCollector;

use Acme\DemoBundle\Services\SomeService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class SomeDataCollector implements DataCollectorInterface
{
    private $service;

    public function __construct(SomeService $service)
    {
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->service->getMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'some_data_collector';
    }
}