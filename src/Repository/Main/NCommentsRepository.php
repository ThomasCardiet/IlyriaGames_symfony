<?php

namespace App\Repository\Main;

use App\Entity\Main\NComments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NComments|null find($id, $lockMode = null, $lockVersion = null)
 * @method NComments|null findOneBy(array $criteria, array $orderBy = null)
 * @method NComments[]    findAll()
 * @method NComments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NCommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NComments::class);
    }

    // /**
    //  * @return NComments[] Returns an array of NComments objects
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
    public function findOneBySomeField($value): ?NComments
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
