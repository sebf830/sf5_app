<?php

namespace App\Controller;

use DateTime;
use App\Entity\Image;
use App\Form\UserType;
use App\Entity\Annonce;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\AnnonceLostType;
use App\Form\AnnonceFoundType;
use App\Factory\ArticleBlogFactory;
use App\Repository\ImageRepository;
use App\Repository\AnnonceRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{

    private EntityManagerInterface $em;
    private FlashyNotifier $flash;
    public function __construct(EntityManagerInterface $em, FlashyNotifier $flash)
    {
        $this->em = $em;
        $this->flash = $flash;
    }

    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(AnnonceRepository $annonceRepo, Request $request): Response
    {

        //password modify
        if ($request->getMethod() == 'POST') {
            $current = $request->request->get('current_password');
            $new = $request->request->get('new_password');
            if ($current === $new) {
                $this->em->flush($this->getUser());
                $this->flash->success('Votre mot de passe est modifié', '');
                return $this->redirectToRoute('app_profile');
            } else {
                $this->flash->error('Votre mot de passe actuel n\'est pas reconnu', '');
                return $this->redirectToRoute('app_profile');
            }
        }
        //temoignages
        return $this->render('profile/index.html.twig');
    }

    #[Route('/profile/modifier-les-informations', name: 'app_update_informations')]
    public function update_informations(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->flash->success('Vos informations sont bien enregistrées : )', '');
            $this->em->flush($user);
        }

        return $this->render('profile/modifier_informations.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/profile/publier-article', name: 'app_publish_article')]
    public function publish_article(Request $request, ArticleBlogFactory $articleFactory): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $articleFactory->makeArticleBlog($article, $this->getUser(),  $form);
            $this->flash->success("L'article est bien enregistré, il sera validé dans 48h", '');
            $this->em->persist($article);
            $this->em->flush();
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/publish_article.html.twig', ['form' => $form->createView()]);
    }



    #[Route('/profile/article/{id}', name: 'app_update_article')]
    public function update_article(Request $request, Article $article, ImageRepository $imageRepo): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $article->getImages();
            $imageName = $form->get('image')->getData();

            if ($imageName) {
                $fichier = md5(uniqid()) . '.' . $imageName->guessExtension();
                $imageName->move($this->getParameter('upload_directory'), $fichier);

                $article->removeImage($images[0]);
                $article->addImage((new Image)->setName($fichier));
                $article->setUpdatedAt(new DateTime());

                $this->em->persist($article);
            }
            $this->em->flush();

            $this->flash->success('L\'article est modifié avec succès )', '');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/modifier_article.html.twig', ['form' => $form->createView(), 'article' => $article]);
    }

    #[Route('/supprimer/annonce/{id}', name: 'app_delete_annonce', methods: ['GET', 'POST', 'DELETE'])]
    public function delete_article(Request $request, Annonce $annonce)
    {
        $submittedToken = $request->request->get('csrf_token_' . $annonce->getId());
        if ($this->isCsrfTokenValid('annonce_delete_' . $annonce->getId(), $submittedToken)) {
            $this->em->remove($annonce);
            $this->em->flush();
            $this->flash->success('Annonce correctement supprimé', '');
        }
        return $this->redirectToRoute('app_profile');
    }
}
