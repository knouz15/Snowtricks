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
        copy(__DIR__.'/avatars/boy-icon-man1.jpg',__DIR__.'/../../public/uploads/avatars/boy-icon-man1.jpg');

        $user->setAvatarFilename('boy-icon-man1.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();

        $user = new User();
        $user->setUsername('gogo');
        $user->setEmail('g@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);
        copy(__DIR__.'/avatars/boy-icon-man2.jpg',__DIR__.'/../../public/uploads/avatars/boy-icon-man2.jpg');

        $user->setAvatarFilename('/boy-icon-man2.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();

        $user = new User();
        $user->setUsername('dodo');
        $user->setEmail('d@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);
        copy(__DIR__.'/avatars/boy-icon-man3.jpg',__DIR__.'/../../public/uploads/avatars/boy-icon-man3.jpg');

        $user->setAvatarFilename('/boy-icon-man3.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();

        $user = new User();
        $user->setUsername('soso');
        $user->setEmail('s@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);
        copy(__DIR__.'/avatars/woman-icon1.jpg',__DIR__.'/../../public/uploads/avatars/woman-icon1.jpg');

        $user->setAvatarFilename('/woman-icon1.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();
 
 
        $user = new User();
        $user->setUsername('jojo');
        $user->setEmail('j@y.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        

        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);
        copy(__DIR__.'/avatars/woman-icon2.jpg',__DIR__.'/../../public/uploads/avatars/woman-icon2.jpg');

        $user->setAvatarFilename('/woman-icon2.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();
 
        $user = new User();
        $user->setUsername('fofo');
        $user->setEmail('f@.fr');
        $user->setIsVerified(true);
        $user->setIagreeTerms(new \Datetime());
        
        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);
        copy(__DIR__.'/avatars/woman-icon3.jpg',__DIR__.'/../../public/uploads/avatars/woman-icon3.jpg');

        $user->setAvatarFilename('/woman-icon3.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();     
    }

}
