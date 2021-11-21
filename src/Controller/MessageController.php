<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Repository\UserRepository;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{

    private FlashyNotifier $flash;

    public function __construct(FlashyNotifier $flash)
    {
        $this->flash = $flash;
    }

    #[Route('/send_message', name: 'send_message', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $em): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response('La requete à renvoyé un problème', 400);
        }

        $data = json_decode($request->getContent(), true);
        $announcer  = $em->getRepository(User::class)->findOneBy(['id' => $data['id']]);

        $message = (new Message())
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setSender($this->getUser())
            ->setReceiver($announcer)
            ->setCreatedAt(new \DateTimeImmutable());

        $em->persist($message);
        $em->flush();

        $this->flash->success('Votre message à bien été envoyé, vous allez recevoir une notification par email', '');
        return new JsonResponse(json_encode(array('success' => 'Votre message est envoyé')), 200);
    }
}
