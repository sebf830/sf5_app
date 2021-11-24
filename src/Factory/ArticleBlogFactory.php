<?php

namespace App\Factory;

use DateTime;
use App\Entity\User;
use App\Entity\Image;
use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleBlogFactory extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function makeTemoignage(array $data, User $user, Category $category): void
    {
        $article = (new Article)
            ->setTitle($data['title'] ?? null)
            ->setContent($data['content'])
            ->setAuthor($user)
            ->setDate(new DateTime())
            ->setUpdatedAt(null)
            ->setCategory($category)
            ->setPublicationStatus(0);
        $this->em->persist($article);
        $article->setSlug($article->getTitle() != null ? $this->createSlug($article->getTitle()) : null);
        $this->em->persist($article);
        $this->em->flush();
    }

    public function makeArticleBlog(Article $article, User $user, FormInterface $form): void
    {
        $image = $form->get('image')->getData();
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $image->move($this->getParameter('upload_directory'), $fichier);
        $image = new Image();
        $image->setName($fichier);
        $this->em->persist($image);

        $article->setAuthor($user);
        $article->setDate(new DateTime());
        $article->setUpdatedAt(null);
        $article->addImage($image);
        $article->setSlug($this->createSlug($article->getTitle()));
        $article->setPublicationStatus(0);
    }

    public function createSlug(string $chaine): string
    {
        $check = ['!', '?', "'", '.', ';', ','];
        $slug = str_replace(' ', '-', strtolower($chaine));
        $slug = str_replace($check, '', $slug);
        return $slug;
    }
}
