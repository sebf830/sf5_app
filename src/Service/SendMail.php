<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class SendMail
{

    public function sendEmail(MailerInterface $mailer, $to, $subject, $html)
    {
        $email = (new TemplatedEmail())
            ->from('admin@donne-la-patte.fr')
            ->to($to)
            ->subject($subject)
            ->html($html);

        $mailer->send($email);
    }

    public function sendContactMessage(MailerInterface $mailer, $from,  $html)
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to('admin@donne-la-patte.fr')
            ->html($html);

        $mailer->send($email);
    }
}
