<?php

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Annonce;
use App\Entity\Article;
use App\Entity\Message;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class DatasExtension extends AbstractExtension
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('userStatistics', [$this, 'userStatistics'], ['is_safe' => ['html']]),
            new TwigFilter('annonceStatistics', [$this, 'annonceStatistics'], ['is_safe' => ['html']]),
            new TwigFilter('publishedArticleStatistics', [$this, 'publishedArticleStatistics'], ['is_safe' => ['html']]),
            new TwigFilter('unpublishedArticleStatistics', [$this, 'publishedArticleStatistics'], ['is_safe' => ['html']]),
            new TwigFilter('messageStatistics', [$this, 'messageStatistics'], ['is_safe' => ['html']]),
        ];
    }

    public function userStatistics(): int
    {
        $all = $this->em->getRepository(User::class)->findAll();
        return count($all);
    }
    public function annonceStatistics(): int
    {
        $all = $this->em->getRepository(Annonce::class)->findAll();
        return count($all);
    }
    public function publishedArticleStatistics(): int
    {
        $all = $this->em->getRepository(Article::class)->findBy(['publication_status' => 1]);
        return count($all);
    }
    public function messageStatistics(): int
    {
        $all = $this->em->getRepository(Message::class)->findAll();
        return count($all);
    }
}
