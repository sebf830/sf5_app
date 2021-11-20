<?php

namespace App\Repository;

use App\Class\Search;
use App\Entity\Annonce;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function findSearch(Search $search)
    {
        $query = $this->createQueryBuilder('a')
            ->select('n', 'a')
            ->join('a.animal', 'n')
            ->join('a.user', 'u');

        if ($search->type == 1) {
            $query = $query
                ->andWhere('n.isLost = 1')
                ->andWhere('n.type = :chien')
                ->setParameter('chien', 'chien');
        } elseif ($search->type == 2) {
            $query = $query
                ->andWhere('n.isLost = 1')
                ->andWhere('n.type = :chat')
                ->setParameter('chat', 'chat');
        } elseif ($search->type == 3) {
            $query = $query
                ->andWhere('n.isLost = 0')
                ->andWhere('n.type = :chien')
                ->setParameter('chien', 'chien');
        } elseif ($search->type == 4) {
            $query = $query
                ->andWhere('n.isLost = 0')
                ->andWhere('n.type = :chat')
                ->setParameter('chat', 'chat');
        }

        if (!empty($search->date)) {
            $date = new DateTime($search->date->format('Y-m-d H:i:s'));
            if ($search->type == 1 || $search->type == 2) {
                $query = $query
                    ->andWhere('a.lostAt > :down AND a.lostAt < :up')
                    ->setParameter('down', $date->modify('- 1 month'))
                    ->setParameter('up', $date->modify('+ 1 month'));
            } elseif ($search->type == 3 || $search->type == 4) {
                $query = $query
                    ->andWhere('a.foundAt BETWEEN :down AND :up')
                    ->setParameter('down', $date->modify('- 1 month'))
                    ->setParameter('up', $date->modify('+ 1 month'));
            }
        }
        if (!empty($search->city)) {
            $query = $query
                ->andWhere('u.city LIKE :city')
                ->setParameter('city', '%' .  $search->city . '%');
        }
        if (!empty($search->race)) {
            $query = $query
                ->andWhere('n.race LIKE :race')
                ->setParameter('race', '%' . $search->race . '%');
        }
        if (!empty($search->gender)) {
            $query = $query
                ->andWhere('n.gender = :gender')
                ->setParameter('gender', $search->gender);
        }
        if (!empty($search->color)) {
            $query = $query
                ->andWhere('n.color LIKE :color')
                ->setParameter('color', '%' . $search->color . '%');
        }
        return $query->getQuery()->getResult();
    }
}
