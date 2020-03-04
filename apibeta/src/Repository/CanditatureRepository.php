<?php

namespace App\Repository;

use App\Entity\Canditature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Canditature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Canditature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Canditature[]    findAll()
 * @method Canditature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CanditatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Canditature::class);
    }

    // /**
    //  * @return Canditature[] Returns an array of Canditature objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Canditature
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
