<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use DateTimeImmutable;
use App\Service\SendMail;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{

    private $mailer;
    private $flash;
    private $em;

    public function __construct(MailerInterface $mailer, FlashyNotifier $flash, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->flash = $flash;
        $this->em = $em;
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        if ($request->get('email')) {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);

            if ($user) {
                // on crée un Reset
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTimeImmutable());
                $this->em->persist($reset_password);
                $this->em->flush();

                //on envoie un email avec un lien de reinitialisation
                $url = $this->generateUrl('update_password', ['token' => $reset_password->getToken()]);

                $mail = new SendMail();
                $mail->sendEmail(
                    $this->mailer,
                    $user->getEmail(),
                    'Votre demande de renouvellement de mot de passe',
                    '<h5>Mon nouveau password</h5>
			        <p>Bonjour vous avez fait une demande de renouvellement de mot de passe<br>
				    Cliquez sur le lien pour le récupérer</p>
			        <a href="http://donne-la-patte-dev:8546' . $url . '" style="color:white; padding:7px; background-color:lightblue;border-radius:8px;">Renouveller</a>'
                );

                $this->flash->success('Vous allez recevoir un mail de réinitialisation', '');
                return $this->redirectToRoute('reset_password');
            } else {
                $this->flash->error('Adresse inconnue, veuillez réessayer', '');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-password/{token}', name: 'update_password')]
    public function update($token, Request $request, UserPasswordHasherInterface $hasher)
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);

        //  on verifie que le user à bien un token de reset
        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        //on verifie l'expiration du token
        $now = new DateTimeImmutable();
        if ($now > $reset_password->getCreatedAt()->modify('+ 3 hour')) {
            $this->flash->error('Votre demande de mot de passe à expiré, veuillez la renouveller', '');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_password = $form->get('new_password')->getData();
            $password = $hasher->hashPassword($reset_password->getUser(), $new_password);
            $reset_password->getUser()->setPassword($password);
            $this->em->flush();

            $this->flash->success('mot de passe mis à jour', '');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', ['form' => $form->createView()]);
    }
}
