<?php

namespace App\Repository\Main;

use App\Entity\Main\FSubcategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FSubcategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FSubcategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FSubcategory[]    findAll()
 * @method FSubcategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FSubcategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FSubcategory::class);
    }

    // /**
    //  * @return FSubcategory[] Returns an array of FSubcategory objects
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
    public function findOneBySomeField($value): ?FSubcategory
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function search($value, $category_id) {

        if($category_id == null) {
            return $this->createQueryBuilder('u')
                ->andWhere('u.name LIKE :value')
                ->setParameter('value', '%'.$value.'%')
                ->getQuery()
                ->execute();
        }

        return $this->createQueryBuilder('u')
            ->where('u.category = :category_id')
            ->andWhere('u.name LIKE :value')
            ->setParameter('category_id', $category_id)
            ->setParameter('value', '%'.$value.'%')
            ->getQuery()
            ->execute();
    }
}
