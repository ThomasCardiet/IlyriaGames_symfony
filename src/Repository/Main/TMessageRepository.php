<?php

namespace App\Repository\Main;

use App\Entity\Main\TMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TMessage[]    findAll()
 * @method TMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TMessage::class);
    }

    // /**
    //  * @return TMessage[] Returns an array of TMessage objects
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
    public function findOneBySomeField($value): ?TMessage
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
