<?php

namespace App\Repository;

use App\Entity\CollaborateurRestaurantFiltre;
use App\Entity\Restaurant;
use App\Entity\RestaurantFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findAllWithFilter(RestaurantFiltre $filter): QueryBuilder
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

        return $queryBuilder->orderBy('r.nom', 'ASC');
    }

    public function findCurrentAffectationsWithFilter($restaurantId, CollaborateurRestaurantFiltre $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('a.id, c.nom, c.prenom, c.email, f.intitule, a.dateDebut, a.dateFin')
            ->leftJoin('r.affectations', 'a')
            ->leftJoin('a.collaborateur', 'c')
            ->leftJoin('a.fonction', 'f')
            ->andWhere('r.id = :restaurantId')
            ->andWhere('a.dateDebut <= CURRENT_DATE()')
            ->andWhere('a.dateFin IS NULL OR a.dateFin > CURRENT_DATE()')
            ->setParameter('restaurantId', $restaurantId);

        if ($filter->getNom()) {
            $queryBuilder
                ->andWhere('LOWER(c.nom) LIKE :nom')
                ->setParameter('nom', '%' . strtolower($filter->getNom()) . '%');
        }

        if ($filter->getDebut()) {
            $queryBuilder
                ->andWhere('a.dateDebut = :debut')
                ->setParameter('debut', $filter->getDebut());
        }

        if ($filter->getFonction()) {
            $queryBuilder
                ->andWhere('f = :fonction')
                ->setParameter('fonction', $filter->getFonction());
        }

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC');
    }

    public function findAllAffectationsWithFilter($restaurantId, CollaborateurRestaurantFiltre $filter): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('a.id, c.nom, c.prenom, c.email, f.intitule, a.dateDebut, a.dateFin')
            ->leftJoin('r.affectations', 'a')
            ->leftJoin('a.collaborateur', 'c')
            ->leftJoin('a.fonction', 'f')
            ->andWhere('r.id = :restaurantId')
            ->setParameter('restaurantId', $restaurantId);

        if ($filter->getNom()) {
            $queryBuilder
                ->andWhere('LOWER(c.nom) LIKE :nom')
                ->setParameter('nom', '%' . strtolower($filter->getNom()) . '%');
        }

        if ($filter->getDebut()) {
            $queryBuilder
                ->andWhere('a.dateDebut = :debut')
                ->setParameter('debut', $filter->getDebut());
        }

        if ($filter->getFonction()) {
            $queryBuilder
                ->andWhere('f = :fonction')
                ->setParameter('fonction', $filter->getFonction());
        }

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
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
