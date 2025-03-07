<?php

namespace App\Repository;

use App\Entity\WishList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WishList>
 */
class WishListRepository extends ServiceEntityRepository
{
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishList::class);
    }

    
    public function findByUserId(int $userId): ?WishList
    {
        return $this->createQueryBuilder('w')
            ->innerJoin('w.user', 'u')
            ->leftJoin('w.item', 'wi') 
            ->addSelect('wi') // This ensures that the items are loaded as well
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
    


    /**
     * Finds a specific item in a wishlist.
     *
     * @param int $wishListId
     * @param int $itemId
     * @return WishList|null
     */
    public function findItemInWishList(int $wishListId, int $itemId): ?WishList
    {
        return $this->createQueryBuilder('wl')
            ->innerJoin('wl.item', 'i')
            ->andWhere('wl.id = :wishListId')
            ->andWhere('i.id = :itemId')
            ->setParameter('wishListId', $wishListId)
            ->setParameter('itemId', $itemId)
            ->getQuery()
            ->getOneOrNullResult();
    }






    /**
     * @return WishList|null Returns a WishList object or null
     */
    // public function findByUserId(int $userId): ?WishList
    // {
    //     return $this->createQueryBuilder('w')
    //         ->innerJoin('w.user', 'u')
    //         ->andWhere('u.id = :userId')
    //         ->setParameter('userId', $userId)
    //         ->getQuery()
    //         ->getOneOrNullResult();
    // }


    //    /**
    //     * @return WishList[] Returns an array of WishList objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WishList
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
