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
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chien', 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lost/lostDogs.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/annonce-chiens-trouves', name: 'app_found_dogs', methods: ['GET'])]
    public function getFoundDogs(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chien', 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/found/foundDogs.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/annonce-chats-perdus', name: 'app_lost_cats', methods: ['GET'])]
    public function getLostCats(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chat', 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lost/lostCats.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }


    #[Route('/annonce-chats-trouves', name: 'app_found_cats', methods: ['GET'])]
    public function getFoundCats(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chat', 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/found/foundCats.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/annonce-animaux-trouves', name: 'app_found_animals', methods: ['GET'])]
    public function getLostAnimals(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus(null, 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 8);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/found/foundAnimals.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/annonce-animaux-perdus', name: 'app_lost_animals', methods: ['GET'])]
    public function getFoundAnimails(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus(null, 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 8);
        $total = $this->annonceRepo->getTotalAnnonce();

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lost/lostAnimals.html.twig', ['annonces' => $annonces, 'total' => $total]);
    }

    #[Route('/rechercher-animal', name: 'app_search_animal', methods: ['GET'])]
    public function searchAnimal(PaginatorInterface $paginator, Request $request): Response
    {
        $total = $this->annonceRepo->getTotalAnnonce();


        return $this->render('annonces/search/search_annonce.html.twig', ['total' => $total]);
    }
}
