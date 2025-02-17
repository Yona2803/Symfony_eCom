<?php

namespace App\Repository;

use App\Entity\Items;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Items>
 */
class ItemsRepository extends ServiceEntityRepository
{



    public const PAGINATOR_PER_PAGE = 12;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Items::class);
    }



    /**
     * @return Items[] Returns an array of Items objects
     */
    public function findByCategoryName(string $categoryName)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.category', 'c')
            ->andWhere('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->getQuery()
            ->getResult();
    }



    public function findByTag(string $tag)
    {
        return $this->createQueryBuilder('i')
            ->where('i.tags LIKE :tag')
            ->setParameter('tag', '%' . $tag . '%')
            ->getQuery()
            ->getResult();
    }



    public function findByPartialName(string $name): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%' . (string)$name . '%')
            ->getQuery()
            ->getResult();
    }



    public function findProducts(int $offset, int $limit = self::PAGINATOR_PER_PAGE): Paginator
    {
        // Validate offset and limit
        if ($offset < 0 || $limit < 1) {
            throw new \InvalidArgumentException('Invalid offset or limit.');
        }

        $query = $this->createQueryBuilder('i')
            ->addSelect('i.id, i.name, i.stock, i.price, i.itemImage')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        // Use Doctrine's Paginator with fetchJoinCollection for better performance
        return new Paginator($query, $fetchJoinCollection = true);
    }





    //    /**
    //     * @return Items[] Returns an array of Items objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Items
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


}
