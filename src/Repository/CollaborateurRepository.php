<?php

namespace App\Repository;

use App\Entity\Collaborateur;
use App\Entity\CollaborateurFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Collaborateur>
 */
class CollaborateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaborateur::class);
    }

    public function findAllWithFilter(CollaborateurFiltre $filter): QueryBuilder
    {
        // Requête pour obtenir les collaborateurs avec leurs affectations actuelles
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin(
                'c.affectations',
                'a',
                'WITH',
                'a.dateFin IS NULL OR a.dateFin >= CURRENT_DATE()'
            );


        if ($filter->getPrenom()) {
            $queryBuilder
                ->andWhere('LOWER(c.prenom) LIKE :prenom')
                ->setParameter('prenom', '%' . strtolower($filter->getPrenom()) . '%');
        }

        if ($filter->getNom()) {
            $queryBuilder
                ->andWhere('LOWER(c.nom) LIKE :nom')
                ->setParameter('nom', '%' . strtolower($filter->getNom()) . '%');
        }

        if ($filter->getEmail()) {
            $queryBuilder
                ->andWhere('LOWER(c.email) LIKE :email')
                ->setParameter('email', '%' . strtolower($filter->getEmail()) . '%');
        }

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC');
    }



    public function findAllWithoutAffectation(): array
    {
        // Requête pour obtenir les collaborateurs avec leurs affectations actuelles
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin(
                'c.affectations',
                'a',
                'WITH',
                'a.dateFin IS NULL OR a.dateFin >= CURRENT_DATE()'
            )
            ->leftJoin('a.fonction', 'f')
            ->addSelect('a', 'f')
            ->where('f IS NULL');

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Collaborateur[] Returns an array of Collaborateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Collaborateur
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
