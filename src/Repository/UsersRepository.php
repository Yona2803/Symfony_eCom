<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @extends ServiceEntityRepository<Users>
 */
class UsersRepository extends ServiceEntityRepository
{


    public const PAGINATOR_PER_PAGE = 6;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function add(Users $user, bool $flush = false): void
    {
        $this->getEntityManager()->persist($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findOneById($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findAllAdmins(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', (string)'%' . $role . '%')
            ->getQuery()
            ->getResult();
    }


    public function findCustomerByRoles(int $offset, int $limit = self::PAGINATOR_PER_PAGE, string $role): Paginator
    {

        // Validate offset and limit
        if ($offset < 0 || $limit < 1) {
            throw new \InvalidArgumentException('Invalid offset or limit.');
        }

        $query = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', (string)'%' . $role . '%')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        // Use Doctrine's Paginator with fetchJoinCollection for better performance
        return new Paginator($query, $fetchJoinCollection = true);
    }




    public function deleteById($id): bool
    {
        $query = $this->createQueryBuilder('u')
            ->delete()
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $result = $query->execute();

        return $result > 0 ? true : false;
    }
}
