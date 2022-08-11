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
        $user->setAvatarFilename('boy-icon-man-icon-avatar-icon-NK0qgMNF_t.jpg');
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
        $user->setAvatarFilename('imgbin-avatar-icon-3d-character-icon-material-woman-wearing-blue-suit-cector-HJBJAK3EFdaXKzZQ6SuTXDUJb_t.jpg');
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
        $user->setAvatarFilename('/imgbin-scalable-graphics-avatar-icon-ms-curly-hair-DUUCHSqTJbkFz6FReXgD9gcTH_t.jpg');
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
        $user->setAvatarFilename('/man-icon-avatars-icon-avatar-icon-zbnC5PFp_t.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
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
        $user->setAvatarFilename('/young-icon-boy-icon-avatar-icon-vtUqbnUb_t.jpg');
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
        $user->setAvatarFilename('/boy-icon-man-icon-avatar-icon-2SqTupUC_t.jpg');
        $manager->persist($user);
        $this->addReference($user->getUsername(),$user );
        $manager->flush();     
    }
}
