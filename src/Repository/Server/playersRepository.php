<?php

namespace App\Repository\Server;

use App\Entity\Server\players;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method players|null find($id, $lockMode = null, $lockVersion = null)
 * @method players|null findOneBy(array $criteria, array $orderBy = null)
 * @method players[]    findAll()
 * @method players[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class playersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, players::class);
    }

    // /**
    //  * @return players[] Returns an array of players objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?players
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
