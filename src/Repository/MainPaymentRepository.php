<?php

namespace App\Repository;

use App\Entity\MainPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MainPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainPayment[]    findAll()
 * @method MainPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainPayment::class);
    }

    // /**
    //  * @return MainPayment[] Returns an array of MainPayment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MainPayment
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
