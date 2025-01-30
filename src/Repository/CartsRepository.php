<?php

namespace App\Repository;

use App\Entity\Carts;
use App\Entity\CartItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carts>
 */
class CartsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carts::class);
    }

    //    /**
    //     * @return Carts[] Returns an array of Carts objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Carts
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }



public function findCartByUserId(int $userId): ?Carts
{
    return $this->createQueryBuilder('c')
        ->innerJoin('c.user', 'u')
        ->andWhere('u.id = :userId')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getOneOrNullResult();
}

public function findItemInCart(int $cartId, int $itemId): ?CartItems
{
    return $this->createQueryBuilder('ci')
        ->andWhere('ci.cart = :cartId')
        ->andWhere('ci.item = :itemId')
        ->setParameter('cartId', $cartId)
        ->setParameter('itemId', $itemId)
        ->getQuery()
        ->getOneOrNullResult();
}

}
