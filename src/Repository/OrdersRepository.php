<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{

    public const PAGINATOR_PER_PAGE = 6;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function findOrderDetails()
    {
        return $this->createQueryBuilder('o')
            ->select(
                'o.id AS orderId, o.orderDate, o.totalAmount,
            u.id AS userId, u.username, u.firstName, u.lastName, u.email,
            os.statusName'
            )
            ->join('o.orderStatus', 'os')
            ->join('o.user', 'u')
            ->orderBy('orderId', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findOrders($Caller)
    {
        $Orders = $this->createQueryBuilder('o')  // 'o' is alias for orders
            ->select(
                'o.id, o.orderDate, o.totalAmount,
            os.statusName, COUNT(od.id) AS detailsCount'
            )
            ->join('o.orderStatus', 'os')
            ->join('o.orderDetails', 'od')
            ->groupBy('o.id')
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getArrayResult();



public function findOrderDetails(int $offset, int $limit = self::PAGINATOR_PER_PAGE): Paginator
{
    // Validate offset and limit
    if ($offset < 0 || $limit < 1) {
        throw new \InvalidArgumentException('Invalid offset or limit.');
    }

    $query = $this->createQueryBuilder('o')
        ->addSelect(
            'o.id AS orderId', 
            'o.orderDate', 
            'o.totalAmount',
            'u.id AS userId', 
            'u.username', 
            'u.firstName', 
            'u.lastName', 
            'u.email',
            'os.statusName'
        )
        ->join('o.orderStatus', 'os')
        ->join('o.user', 'u')
        ->orderBy('o.id', 'DESC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery();

    // Use Doctrine's Paginator with fetchJoinCollection for better performance
    return new Paginator($query, $fetchJoinCollection = true);
}



    // public function findOrderDetails()
    // {
    //     return $this->createQueryBuilder('o')
    //         ->select(
    //             'o.id AS orderId, o.orderDate, o.totalAmount,
    //         u.id AS userId, u.username, u.firstName, u.lastName, u.email,
    //         os.statusName'
    //         )
    //         ->join('o.orderStatus', 'os')
    //         ->join('o.user', 'u')
    //         ->orderBy('orderId', 'DESC')
    //         ->getQuery()
    //         ->getArrayResult(); 
    // }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  

        return $Orders;
    }

    //    /**
    //     * @return Orders[] Returns an array of Orders objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Orders
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
