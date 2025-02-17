<?php

namespace App\DataFixtures;

use App\Entity\OrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statusOne = new OrderStatus();
        $statusOne->setStatusName('PREPARING');
        $manager->persist($statusOne);
        
        $statusTwo = new OrderStatus();
        $statusTwo->setStatusName('SHIPPED');
        $manager->persist($statusTwo);
        
        $statusThree = new OrderStatus();
        $statusThree->setStatusName('DELIVERED');
        $manager->persist($statusThree);
        
        $statusFour = new OrderStatus();
        $statusFour->setStatusName('RETURNED');
        $manager->persist($statusFour);
        
        $statusFive = new OrderStatus();
        $statusFive->setStatusName('CANCELED');
        $manager->persist($statusFive);

        $manager->flush();
    }
}
