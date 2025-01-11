<?php

namespace App\Service;

use App\Entity\Items;
use Doctrine\ORM\EntityManagerInterface;


class ItemsService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Items[]
     */
    public function getAllProducts(): array
    {
        return $this->entityManager->getRepository(Items::class)->findAll();
    }


    public function getProductByName(string $name): array
    {
        return $this->entityManager->getRepository(Items::class)->findByPartialName($name);
    }

}