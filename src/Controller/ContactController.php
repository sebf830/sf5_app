<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer, FlashyNotifier $flash): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $contact->setUser($this->getUser());
            }
            $em->persist($contact);
            $em->flush();

            $mail = new SendMail();
            $mail->sendContactMessage(
                $mailer,
                $contact->getEmail(),
                "<h5>Nouveau message de {$contact->getFirstname()} {$contact->getLastname()}</h5>
                    <p>{$contact->getMessage()}</p>"
            );
            $flash->success('Nous avons bien reçu votre message, merci!');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('contact/index.html.twig', ['form' => $form->createView()]);
    }
}
