<?php

namespace SymfonyWorkshop\DependencyInjectionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class GreetingCardController extends AbstractMailingController
{
    public function sendAction($message, $recipient)
    {
        $this->logger->info(sprintf('Sending greeting card "%s" to recipient "%s".', $message, $recipient));
        $this->mailer->sendMail($message, array($recipient));

        return new Response(sprintf('<html><body>GreetingCardController::sendAction with message "%s" to recipient "%s"</body></html>', $message, $recipient));
    }
}
