<?php

namespace App\Repository\Main;

use App\Entity\Main\news;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method news|null find($id, $lockMode = null, $lockVersion = null)
 * @method news|null findOneBy(array $criteria, array $orderBy = null)
 * @method news[]    findAll()
 * @method news[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class newsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, news::class);
    }

    // /**
    //  * @return news[] Returns an array of news objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?news
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
