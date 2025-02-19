<?php

namespace App\Repository;

use App\Entity\OrderDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderDetails>
 */
class OrderDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDetails::class);
    }


    public function getOrderDetailsByOrderId($orderId)
    {
        return $this->createQueryBuilder('od')
            ->select(
                'od.quantity, od.totalPrice,
                o.id AS orderId, o.orderDate, o.totalAmount,
                u.id AS userId, u.username, u.firstName, u.lastName, u.email,
                i.id as itemId, i.name, i.price'
            )
            ->join('od.orderFk', 'o')
            ->join('o.user', 'u')
            ->join('od.item', 'i')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getArrayResult();
    }


    public function findOrderDetailsById($orderId)
    {
        $data = $this->createQueryBuilder('od')
            ->select(
                'o.id as orderId,
                 od.totalPrice, od.quantity, os.statusName,
                 i.id as itemId, i.name as itemName, i.itemImage'
            )
            ->join('od.orderFk', 'o')
            ->join('o.orderStatus', 'os')
            ->join('od.item', 'i')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getArrayResult();

        // Convert the BLOB image to a base64 encoded string with the correct MIME type
        foreach ($data as &$record) {
            if (isset($record['itemImage']) && is_resource($record['itemImage'])) {
                // Detect the MIME type
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer(stream_get_contents($record['itemImage'], -1, 0));

                // Reset the stream pointer to the beginning for reading the content
                rewind($record['itemImage']);

                // Base64 encode the image content and prepend with the MIME type
                $record['itemImage'] = 'data:' . $mimeType . ';base64,' . base64_encode(stream_get_contents($record['itemImage']));
            }
        }

        return $data;
    }




    public function findByOrderId(int $orderId): array
    {
        return $this->createQueryBuilder('od')
            ->andWhere('od.orderFk = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult();
    }

    
    
    //    /**
    //     * @return OrderDetails[] Returns an array of OrderDetails objects
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

    //    public function findOneBySomeField($value): ?OrderDetails
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    
}
