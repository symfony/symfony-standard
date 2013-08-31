<?php

namespace SymfonyWorkshop\DependencyInjectionBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use SymfonyWorkshop\DependencyInjectionBundle\Mailer\MyMailer;

abstract class AbstractMailingController
{
    protected $mailer;
    protected $logger;

    public function __construct(MyMailer $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function isLazyAction()
    {
        $isLazy = in_array('ProxyManager\Proxy\LazyLoadingInterface', class_implements($this->mailer));

        return new Response(sprintf('<html><body>Mailer is lazy: %s</body></html>', var_export($isLazy, true)));
    }
}
