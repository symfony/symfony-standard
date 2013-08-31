<?php

namespace SymfonyWorkshop\DependencyInjectionBundle\Mailer;

class MyMailer
{
    public function __construct()
    {
        // Simulates a service that is heavy to instantiate.
        // So it would be better to lazy load it.
        sleep(3);
    }

    public function sendMail($message, array $recipients)
    {
        // not implemented
    }
}
