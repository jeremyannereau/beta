<?php

namespace App\Repository;

use App\Entity\FormationUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FormationUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormationUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormationUsers[]    findAll()
 * @method FormationUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationUsers::class);
    }

    // /**
    //  * @return FormationUsers[] Returns an array of FormationUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FormationUsers
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
