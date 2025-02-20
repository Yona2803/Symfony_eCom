<?php

namespace App\Faker;

use App\Entity\Items;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\ItemPasswordHasherInterface;
use Faker\Factory;


class ProductsFaker
{

    public function __construct(
        private EntityManagerInterface $em,
        private CategoriesRepository $categoriesRepository
    ) {
        $this->em = $em;
        $this->categoriesRepository = $categoriesRepository;
    }


    public function createRandomItems(int $count = 10)
    {
        // Initialize Faker
        $faker = Factory::create();
        
        $category = $this->categoriesRepository->findOneBy(['id' => 2]);
        for ($i = 0; $i < $count; $i++) {

            $item = new Items();
            
            // Set random data using Faker
            $item->setName($faker->name);
            $item->setDescription($faker->sentence);
            $item->setPrice($faker->randomFloat(2, 10, 1000)); // Random price between 10 and 1000 with 2 decimal places
            $item->setStock($faker->numberBetween(1, 100));
            $item->setTags($faker->words(3)); // Random tags (3 words)
            $item->setCategory($category);

            // Generate a random image URL and convert it to binary data
            $imageUrl = "https://loremflickr.com/640/480/"; // generate a random image URL from picsum.photos
            $binaryData = file_get_contents($imageUrl);
            $item->setItemImage($binaryData);

            $this->em->persist($item);
        }

        $this->em->flush();
    }
}
