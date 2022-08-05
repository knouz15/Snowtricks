<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('coco');
        $user->setEmail('c@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('gogo');
        $user->setEmail('g@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('dodo');
        $user->setEmail('d@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('soso');
        $user->setEmail('s@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('toto');
        $user->setEmail('t@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('jojo');
        $user->setEmail('j@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('fofo');
        $user->setEmail('f@.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        
    }
}
