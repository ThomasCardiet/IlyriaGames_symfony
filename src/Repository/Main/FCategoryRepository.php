<?php

namespace App\Repository\Main;

use App\Entity\Main\FCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FCategory[]    findAll()
 * @method FCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FCategory::class);
    }

    // /**
    //  * @return FCategory[] Returns an array of FCategory objects
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
    public function findOneBySomeField($value): ?FCategory
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function search($value) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name LIKE :value')
            ->setParameter('value', '%'.$value.'%')
            ->getQuery()
            ->execute();
    }
}
