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

    public function findOrders(string $Option, int $User_id): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select(
                'o.id, 
             o.orderDate, 
             o.totalAmount,
                u.id AS userId,
             ost.statusName, 
             COUNT(od.id) AS detailsCount,
             s.name AS state, 
             ss.name AS stateStatus'
            )
            ->leftJoin('o.user', 'u')
            ->leftJoin('o.orderDetails', 'od')
            ->leftJoin('o.orderState', 'os')
            ->leftJoin('os.State', 's')
            ->leftJoin('os.StateStatus', 'ss')
            ->leftJoin('o.orderStatus', 'ost')
            ->groupBy('o.id', 'u.id', 'ost.statusName', 's.name', 'ss.name')
            ->orderBy('o.id', 'DESC')
            ->Where('u.id = :id')
            ->setParameter('id', $User_id);

        if ($Option !== 'Order') {
            $qb->andWhere('s.name = :caller')
                ->setParameter('caller', $Option);
        }

        return $qb->getQuery()->getArrayResult();
    }




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
        ->join('o.user', 'u')
        ->join('o.orderStatus', 'os')
        ->leftJoin('o.orderState', 'orderState')
        ->leftJoin('orderState.StateStatus', 'stateStatus') 
        ->where(
            'os.statusName LIKE :preparing 
            OR os.statusName LIKE :shipped
            OR os.statusName LIKE :delivered'
        )
        ->andWhere(
            'stateStatus.name LIKE :pending
            OR stateStatus.name LIKE :declined 
            OR stateStatus.name IS NULL')
        ->setParameter('preparing', 'Preparing')
        ->setParameter('shipped', 'Shipped')
        ->setParameter('delivered', 'Delivered')
        ->setParameter('pending', 'Pending')
        ->setParameter('declined', 'Declined')
        ->orderBy('o.id', 'DESC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery();

    // Use Doctrine's Paginator with fetchJoinCollection for better performance
    return new Paginator($query, $fetchJoinCollection = true);
}


    public function findOrderDetailsWithoutPagination(): array
{
    return $this->createQueryBuilder('o')
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
        ->join('o.user', 'u')
        ->join('o.orderStatus', 'os')
        ->leftJoin('o.orderState', 'orderState')
        ->leftJoin('orderState.StateStatus', 'stateStatus') 
        ->where(
            'os.statusName LIKE :preparing 
            OR os.statusName LIKE :shipped
            OR os.statusName LIKE :delivered'
        )
        ->andWhere(
            'stateStatus.name LIKE :pending
            OR stateStatus.name LIKE :declined 
            OR stateStatus.name IS NULL')
        ->setParameter('preparing', 'Preparing')
        ->setParameter('shipped', 'Shipped')
        ->setParameter('delivered', 'Delivered')
        ->setParameter('pending', 'Pending')
        ->setParameter('declined', 'Declined')
        ->orderBy('o.id', 'DESC')
        ->getQuery()
        ->getResult();
}



    public function getOrderDetailsByStateStatus(int $offset, int $limit = self::PAGINATOR_PER_PAGE): Paginator
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
                'os.statusName,
                state.name AS stateName,
                stateStatus.name AS stateStatusName'
            )
            ->join('o.orderStatus', 'os')
            ->join('o.user', 'u')
            ->join('o.orderState', 'orderState')
            ->join('orderState.StateStatus', 'stateStatus')
            ->join('orderState.State', 'state')
            ->Where(
                'stateStatus.name like :pending'
            )
            ->setParameter('pending', 'Pending')
            // ->setParameter('declined', 'Declined')
            // ->setParameter('accepted', 'Accepted')
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
