<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnoncesController extends AbstractController
{

    private AnnonceRepository $annonceRepo;
    public function __construct(AnnonceRepository $annonceRepo)
    {
        $this->annonceRepo = $annonceRepo;
    }

    #[Route('/annonce-no-{numero}', name: 'annonce_show', methods: ['GET'])]
    public function index($numero): Response
    {
        $annonce = $this->annonceRepo->findOneBy(['numero' => $numero]);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonce) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/index.html.twig', ['annonce' => $annonce, 'total' => $total]);
    }

    #[Route('/annonce-chiens-perdus', name: 'app_lost_dogs', methods: ['GET'])]
    public function getLostDogs(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->search('chien', 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lostDogs.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/annonce-chiens-trouves', name: 'app_found_dogs', methods: ['GET'])]
    public function getFoundDogs(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->search('chien', 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/foundDogs.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/annonce-chats-perdus', name: 'app_lost_cats', methods: ['GET'])]
    public function getLostCats(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->search('chat', 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lostCats.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }


    #[Route('/annonce-chats-trouves', name: 'app_found_cats', methods: ['GET'])]
    public function getFoundCats(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->search('chat', 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/foundCats.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }
}
