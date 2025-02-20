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
        $statusOne->setStatusName('Preparing');
        $manager->persist($statusOne);
        
        $statusTwo = new OrderStatus();
        $statusTwo->setStatusName('Shipped');
        $manager->persist($statusTwo);
        
        $statusThree = new OrderStatus();
        $statusThree->setStatusName('Delivered');
        $manager->persist($statusThree);
        
        $statusFour = new OrderStatus();
        $statusFour->setStatusName('Returned');
        $manager->persist($statusFour);
        
        $statusFive = new OrderStatus();
        $statusFive->setStatusName('Cancelled');
        $manager->persist($statusFive);

        $manager->flush();
    }
}
