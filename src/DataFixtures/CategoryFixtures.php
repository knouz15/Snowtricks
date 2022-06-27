<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $categories = ['Rotations','Flips','Grabs','Slides'];
        foreach($categories as $category){
            $cat= new Category();
                $cat->setNom($category);
                $manager->persist($cat);
        }
       
        

        $manager->flush();
    }
}
