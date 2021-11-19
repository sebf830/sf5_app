<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnoncesController extends AbstractController
{
    #[Route('/annonce-no-{numero}', name: 'annonce_show', methods: ['GET'])]
    public function index($numero,  AnnonceRepository $annonceRepo): Response
    {
        $annonce = $annonceRepo->findOneBy(['numero' => $numero]);
        $total = $annonceRepo->getTotalAnnonce();

        if (!$annonce) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/index.html.twig', ['annonce' => $annonce, 'total' => $total]);
    }
}
