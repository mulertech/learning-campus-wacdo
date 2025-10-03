<?php

namespace App\Repository;

use App\Entity\Affectation;
use App\Entity\AffectationFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Affectation>
 */
class AffectationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affectation::class);
    }

    public function findAllWithFilter(AffectationFiltre $filter)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.restaurant', 'r')
            ->leftJoin('a.fonction', 'f')
            ->leftJoin('a.collaborateur', 'c')
            ->addSelect('r', 'f', 'c');

        if ($filter->getRestaurant()) {
            $queryBuilder
                ->andWhere('r = :restaurant')
                ->setParameter('restaurant', $filter->getRestaurant());
        }

        if ($filter->getFonction()) {
            $queryBuilder
                ->andWhere('f = :fonction')
                ->setParameter('fonction', $filter->getFonction());
        }

        // Overlap date range filter
        if ($filter->getDebut() || $filter->getFin()) {
            if ($filter->getDebut()) {
                $queryBuilder->andWhere('a.dateFin >= :debut OR a.dateFin IS NULL')
                    ->setParameter('debut', $filter->getDebut());
            }

            if ($filter->getFin()) {
                $queryBuilder->andWhere('a.dateDebut <= :fin')
                    ->setParameter('fin', $filter->getFin());
            }
        }

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function isCollaborateurAffecte(Affectation $affectation): bool {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('count(a.id)')
            ->where('a.collaborateur = :collaborateur')
            ->andWhere('a.dateDebut <= :date')
            ->andWhere($qb->expr()->orX(
                'a.dateFin IS NULL',
                'a.dateFin >= :date'
            ))
            ->setParameter('collaborateur', $affectation->getCollaborateur())
            ->setParameter('date', $affectation->getDateDebut());

        if ($affectation->getId()) {
            // Exclude the current affectation if it already exists in the database
            $qb->andWhere('a != :affection')
                ->setParameter('affectation', $affectation);
        }

        $count = (int) $qb->getQuery()->getSingleScalarResult();

        return $count > 0;
    }

    //    /**
    //     * @return Affectation[] Returns an array of Affectation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Affectation
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
