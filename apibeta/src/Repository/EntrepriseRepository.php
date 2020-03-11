<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Entreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprise[]    findAll()
 * @method Entreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

     /**
      * @return Entreprise[] Returns an array of Entreprise objects
      */
    
    public function findBySelect($nom=null,$secteur=null,$departement=null,$ville=null,$adresse=null)
    {
        $query = $this->createQueryBuilder('c');
        if ($nom){
            $query->andWhere('c.nom LIKE :nom')
            ->setParameter('nom','%'. $nom .'%');

        }
        if ($secteur){
            $query->andWhere('c.secteur LIKE :secteur')
            ->setParameter('secteur',$secteur );

        }
        if ($departement){
            $query->andWhere('c.departement LIKE :departement')
            ->setParameter('departement',$departement );
        }
        if ($adresse){
            $query->andWhere('c.adresse LIKE :adresse')
            ->setParameter('adresse','%'. $adresse . '%' );
        }
        if ($ville){
            $query->andWhere('c.ville LIKE :ville')
            ->setParameter('ville','%'. $ville . '%' );
        }

           $query=$query->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery();
            $query = $query->getResult();
        ;
        return $query;
        
    }
    

    /*
    public function findOneBySomeField($value): ?Entreprise
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
