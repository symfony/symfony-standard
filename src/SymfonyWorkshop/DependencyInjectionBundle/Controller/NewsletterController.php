<?php

namespace SymfonyWorkshop\DependencyInjectionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends AbstractMailingController
{
    public function sendAction($message)
    {
        $this->logger->info(sprintf('Sending newsletter "%s" to all subscribers.', $message));
        $this->mailer->sendMail($message, array('foo@example.com', 'bar@example.com'));

        return new Response(sprintf('<html><body>NewsletterController::sendAction with message "%s"</body></html>', $message));
    }
}
