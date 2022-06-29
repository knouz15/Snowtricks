<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class Snowtrickfixtures extends Fixture
{
    private Generator $faker;
    public function load(ObjectManager $manager): void
    {   
        for ($i=1; $i<= 10; $i++) {
            $trick = new Trick();
        
            // use the factory to create a Faker\Generator instance
            // $faker = Factory::create('fr_FR');
    
            $trick->SetSlug($this->faker->word())
                ->SetDescription($this->faker->words(20))
                ->SetCategory($this->faker->word());

                $manager->persist($trick);
        }
        
        $manager->flush();
    }
}
