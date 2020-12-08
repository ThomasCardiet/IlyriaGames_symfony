<?php

namespace App\Repository\Main;

use App\Entity\Main\FTopic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method fTopic|null find($id, $lockMode = null, $lockVersion = null)
 * @method fTopic|null findOneBy(array $criteria, array $orderBy = null)
 * @method fTopic[]    findAll()
 * @method fTopic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FTopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, fTopic::class);
    }

    // /**
    //  * @return fTopic[] Returns an array of fTopic objects
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
    public function findOneBySomeField($value): ?fTopic
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function search($value, $sub_category_id) {

        if($sub_category_id == null) {
            return $this->createQueryBuilder('u')
                ->andWhere('u.title LIKE :value')
                ->setParameter('value', '%'.$value.'%')
                ->getQuery()
                ->execute();
        }

        return $this->createQueryBuilder('u')
            ->where('u.subcategory = :sub_category_id')
            ->andWhere('u.title LIKE :value')
            ->setParameter('sub_category_id', $sub_category_id)
            ->setParameter('value', '%'.$value.'%')
            ->getQuery()
            ->execute();
    }
}
