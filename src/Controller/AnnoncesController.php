<?php

namespace App\Controller;

use App\Class\Search;
use App\Entity\Annonce;
use App\Entity\Message;
use App\Form\SearchType;
use App\Form\MessageType;
use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Serializer\csv\AnimalRaceSerializer;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnoncesController extends AbstractController
{

    private AnimalRaceSerializer $raceSerializer;
    private AnnonceRepository $annonceRepo;
    private FlashyNotifier $flash;

    public function __construct(AnnonceRepository $annonceRepo, AnimalRaceSerializer $raceSerializer, FlashyNotifier $flash)
    {
        $this->annonceRepo = $annonceRepo;
        $this->raceSerializer = $raceSerializer;
        $this->flash = $flash;
    }

    #[Route('/annonce-no-{numero}', name: 'annonce_show', methods: ['GET'])]
    public function index($numero): Response
    {
        $annonce = $this->annonceRepo->findOneBy(['numero' => $numero]);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        if (!$annonce) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/index.html.twig', ['annonce' => $annonce,  'numero' => $numero, 'form' => $form->createView()]);
    }

    #[Route('/annonce-chiens-perdus', name: 'app_lost_dogs', methods: ['GET'])]
    public function getLostDogs(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chien', 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lost/lostDogs.html.twig', ['annonces' => $annonces]);
    }

    #[Route('/annonce-chiens-trouves', name: 'app_found_dogs', methods: ['GET'])]
    public function getFoundDogs(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chien', 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/found/foundDogs.html.twig', ['annonces' => $annonces]);
    }

    #[Route('/annonce-chats-perdus', name: 'app_lost_cats', methods: ['GET'])]
    public function getLostCats(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chat', 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lost/lostCats.html.twig', ['annonces' => $annonces]);
    }


    #[Route('/annonce-chats-trouves', name: 'app_found_cats', methods: ['GET'])]
    public function getFoundCats(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus('chat', 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 5);

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/found/foundCats.html.twig', ['annonces' => $annonces]);
    }

    #[Route('/annonce-animaux-trouves', name: 'app_found_animals', methods: ['GET'])]
    public function getLostAnimals(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus(null, 0);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 8);

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/found/foundAnimals.html.twig', ['annonces' => $annonces]);
    }

    #[Route('/annonce-animaux-perdus', name: 'app_lost_animals', methods: ['GET'])]
    public function getFoundAnimails(PaginatorInterface $paginator, Request $request): Response
    {
        $annoncesDatas = $this->annonceRepo->searchAnnoncesByAnimalAndStatus(null, 1);
        $annonces = $paginator->paginate($annoncesDatas, $request->query->getInt('page', 1), 8);

        if (!$annonces) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('annonces/lost/lostAnimals.html.twig', ['annonces' => $annonces]);
    }

    #[Route('/rechercher-animal', name: 'app_search_animal', methods: ['GET', 'POST'])]
    public function searchAnimal(PaginatorInterface $paginator, Request $request): Response
    {
        $dogRace = $this->raceSerializer->getDataFromFile('race_chien.csv');
        $catRace = $this->raceSerializer->getDataFromFile('race_chat.csv');

        $data = new Search();
        $form = $this->createForm(SearchType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonces = $this->annonceRepo->findSearch($data);
            return $this->render(
                'annonces/search/search_result.html.twig',
                ['form' => $form->createView(), 'dogRace' => $dogRace, 'catRace' => $catRace, 'annonces' => $annonces]
            );
        }

        return $this->render(
            'annonces/search/search_annonce.html.twig',
            ['form' => $form->createView(), 'dogRace' => $dogRace, 'catRace' => $catRace]
        );
    }
}
