<?php

namespace App\Repository;

use App\Entity\Affectation;
use App\Entity\AffectationFiltre;
use App\Entity\Collaborateur;
use App\Entity\CollaborateurAffectationFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findAllWithFilter(AffectationFiltre $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.restaurant', 'r')
            ->leftJoin('a.fonction', 'f')
            ->leftJoin('a.collaborateur', 'c')
            ->addSelect('r', 'f', 'c');

        if ($filter->getVille()) {
            $queryBuilder
                ->andWhere('LOWER(r.ville) LIKE :ville')
                ->setParameter('ville', '%' . strtolower($filter->getVille()) . '%');
        }

        if ($filter->getFonction()) {
            $queryBuilder
                ->andWhere('f = :fonction')
                ->setParameter('fonction', $filter->getFonction());
        }

        // Overlap date range filter
        if ($filter->getDebut() && $filter->getFin()) {
            $queryBuilder->andWhere('a.dateDebut <= :fin AND (a.dateFin >= :debut OR a.dateFin IS NULL)')
                ->setParameter('debut', $filter->getDebut())
                ->setParameter('fin', $filter->getFin());
        } elseif ($filter->getDebut()) {
            $queryBuilder->andWhere('a.dateFin >= :debut OR a.dateFin IS NULL')
                ->setParameter('debut', $filter->getDebut());
        } elseif ($filter->getFin()) {
            $queryBuilder->andWhere('a.dateDebut <= :fin')
                ->setParameter('fin', $filter->getFin());
        }

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC');
    }

    public function findByCollaborateurWithFilter(
        CollaborateurAffectationFiltre $filter,
        Collaborateur $collaborateur
    ): array {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.fonction', 'f')
            ->leftJoin('a.restaurant', 'r')
            ->addSelect('f', 'r')
            ->andWhere('a.collaborateur = :collaborateur')
            ->setParameter('collaborateur', $collaborateur);

        if ($filter->getFonction()) {
            $queryBuilder
                ->andWhere('f = :fonction')
                ->setParameter('fonction', $filter->getFonction());
        }

        if ($filter->getDebut()) {
            $queryBuilder->andWhere('a.dateDebut = :debut')
                ->setParameter('debut', $filter->getDebut());
        }

        return $queryBuilder
            ->orderBy('a.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function isCollaborateurAffecte(Affectation $affectation): bool {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('count(a.id)')
            ->where('a.collaborateur = :collaborateur')
            ->setParameter('collaborateur', $affectation->getCollaborateur());

        if ($affectation->getDateFin()) {
            $qb->andWhere('a.dateDebut <= :fin AND (a.dateFin >= :debut OR a.dateFin IS NULL)')
                ->setParameter('debut', $affectation->getDateDebut())
                ->setParameter('fin', $affectation->getDateFin());
        } else {
            $qb->andWhere('a.dateFin >= :debut OR a.dateFin IS NULL')
                ->setParameter('debut', $affectation->getDateDebut());
        }

        if ($affectation->getId()) {
            // Exclude the current affectation if it already exists in the database
            $qb->andWhere('a != :affectation')
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
