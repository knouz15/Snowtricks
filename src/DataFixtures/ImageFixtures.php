<?php

namespace App\DataFixtures;

use App\Entity\Image;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        
        // $image = new Image();
        // $manager->persist($image);

        $manager->flush();
    }
}
