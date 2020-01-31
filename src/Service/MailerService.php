<?php

namespace App\Service;


use Symfony\Contracts\Translation\TranslatorInterface;

class MailerService
{
    private $mailer;
    private $translator;

    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function sendMail($body, $from, $to, $subject = 'Site chatons')
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

}
