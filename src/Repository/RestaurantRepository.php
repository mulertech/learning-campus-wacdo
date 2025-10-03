<?php

namespace App\Repository;

use App\Entity\Restaurant;
use App\Entity\RestaurantFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function findAllWithFilter(RestaurantFiltre $filter)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->addSelect('r');

        if ($filter->getNom()) {
            $queryBuilder
                ->andWhere('LOWER(r.nom) LIKE :nom')
                ->setParameter('nom', '%' . strtolower($filter->getNom()) . '%');
        }

        if ($filter->getCodePostal()) {
            $queryBuilder
                ->andWhere('LOWER(r.codePostal) LIKE :code_postal')
                ->setParameter('code_postal', '%' . strtolower($filter->getCodePostal()) . '%');
        }

        if ($filter->getVille()) {
            $queryBuilder
                ->andWhere('LOWER(r.ville) LIKE :ville')
                ->setParameter('ville', '%' . strtolower($filter->getVille()) . '%');
        }


        return $queryBuilder
            ->orderBy('r.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCurrentAffectations($restaurantId)
    {
        return $this->createQueryBuilder('r')
            ->select('c.nom, c.prenom, c.email, f.intitule, a.dateDebut, a.dateFin')
            ->leftJoin('r.affectations', 'a')
            ->leftJoin('a.collaborateur', 'c')
            ->leftJoin('a.fonction', 'f')
            ->andWhere('r.id = :restaurantId')
            ->andWhere('a.dateDebut <= CURRENT_DATE()')
            ->andWhere('a.dateFin IS NULL OR a.dateFin > CURRENT_DATE()')
            ->setParameter('restaurantId', $restaurantId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Restaurant[] Returns an array of Restaurant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restaurant
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
