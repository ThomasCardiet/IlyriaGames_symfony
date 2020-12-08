<?php

namespace App\Repository\Server;

use App\Entity\Server\beta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method beta|null find($id, $lockMode = null, $lockVersion = null)
 * @method beta|null findOneBy(array $criteria, array $orderBy = null)
 * @method beta[]    findAll()
 * @method beta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class betaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, beta::class);
    }

    // /**
    //  * @return beta[] Returns an array of beta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?beta
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
