<?php


namespace App\Service;

use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

class EmailSenderService
{

    public function __construct(
        private MessageBusInterface $bus,
        private Environment $twig
        )
    {
        $this->bus = $bus;
        $this->twig = $twig;
    }


    public function sendCancellationEmail(string $emailCustomer, string $subject, array $templateParams): void
    {
        // Render the Twig template with the passed parameters
        $bodyHtml = $this->twig->render('MyPages/Emails/customerResponseEmail.html.twig', $templateParams);

        $Email_From = $_ENV['MAILER_SENDER'];
        // Prepare the email
        $email = (new Email())
            ->from($Email_From)
            ->to($emailCustomer)
            ->subject($subject)
            ->text('This is a text email. Please view the HTML version.')
            ->html($bodyHtml);

        // Dispatch the email to the message queue
        $this->bus->dispatch(new SendEmailMessage($email));
    }
}
