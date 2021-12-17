<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(Request $request, AnnonceRepository $annonceRepo): Response
    {

        $limit = 10;
        $page = (int)$request->query->get('page', 1);

        $annonces = $annonceRepo->getPagination($page, $limit);
        //total
        $total = $annonceRepo->getTotalAnnonce();

        return $this->render('home/index.html.twig', [
            'annonces' => $annonces,
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }
}
