<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     *  retourne les annonces avec une limite par page pour la pagination
     *
     * @param [type] $page
     * @param [type] $limit
     * @return array
     */
    public function getPagination(string $page, int $limit): array
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.createdAt')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * recupere le nombre total d'annonces
     * @return int
     */
    public function getTotalAnnonce(): int
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)');
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Recherche les annonces qui concernent les chiens ou les chats perdus
     * @return array 
     */
    public function searchAnnoncesByAnimalAndStatus(string $type = null, string $status = null): array
    {
        $query = $this->createQueryBuilder('a');
        $query->leftJoin('a.animal', 'n');

        if ($type != null) {
            $query->andWhere('n.type = :type')
                ->setParameter('type', $type);
        }

        $query->andWhere('n.isLost = :status')
            ->setParameter('status', $status);

        return $query->getQuery()->getResult();
    }
}
