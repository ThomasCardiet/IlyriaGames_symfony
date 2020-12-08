<?php

namespace App\Repository\Main;

use App\Entity\Main\TCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TCategory[]    findAll()
 * @method TCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TCategory::class);
    }

    // /**
    //  * @return TCategory[] Returns an array of TCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TCategory
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
