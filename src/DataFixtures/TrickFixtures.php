<?php

namespace App\DataFixtures;
use Datetime;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{

	private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        
		// $faker = Factory::create('fr_FR');

        $tricks = [
			
			
			[
				'360','Rotations','rotation horizontale d\'un tours complet',
				'gogo',
				['hUddT6FGCws','GS9MMT_bNn8','_rS2i4-yb6E'],
				['360-1.jpg','360-2.jpg','360-3.jpg']
			],
			
			[
				'front flip','Flips','rotation verticale en avant',
				'gogo',
				['xhvqu2XBvI0','eGJ8keB1-JM','aTTkQ45DUfk'],
				['frontflip-1.jpg','frontflip-2.jpg','frontflip-3.jpg']
			],
			[
				'indy','Grabs','saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière',
				'dodo',
				['iKkhKekZNQ8','6yA3XqjTh_w'],
				['indy-1.jpg','indy-2.jpg','indy-3.jpg','indy-4.jpg']
			],
			[
				'mute','Grabs','saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
				'dodo',
				['4sha5smEUHA','KXDQv7f8JNs','8r_yZfBWCeQ'],
				['mute-1.jpg','mute-2.jpg','mute-3.jpg','mute-4.jpg','mute-5.jpg']
			],
			[
				'sad','Grabs','saisie de la carre backside de la planche, entre les deux pieds, avec la main avant',
				'dodo',
				['KEdFwJ4SWq4'],
				['sad-1.jpg','sad-2.jpg','sad-3.jpg']
			],
			[
				'nose slide','Grabs','glissade avec planche perpendiculaire à la barre de slide avec la barre du coté avant de la planche',
				'dodo',
				['7AB0FZWyrGQ','Iw77dvnNSKk','oAK9mK7wWvw'],
				['noseslide-1.jpg','noseslide-2.jpg','noseslide-3.jpg']
			],
			[
				'tail slide','Grabs','glissade avec planche perpendiculaire à la barre de slide avec la barre du coté arrière de la planche',
				'coco',
				['h_jU7vjmLjU','HRNXjMBakwM','inAxMRSlGS8'],
				['tailslide-1.jpg','tailslide-2.jpg','tailslide-3.jpg']
			],
			[
				'1080','Rotations','rotation horizontale de trois tours complets',
				'coco',
				['j4NfAsszIOk','camHB0Rj4gA'],
				['1080-1.jpg','1080-2.jpg','1080-3.jpg']
			],
			[
				'180','Rotations','rotation horizontale d\'un demi tour',
				'coco',
				['ATMiAVTLsuc','JMS2PGAFMcE','GnYAlEt-s00'],
				['180-1.jpg','180-2.jpg','180-3.jpg']
			],
			
			[
				'back flip','Rotations','rotation verticale en arrière',
				'soso',
				['AMsWP9WJS_0','SlhGVnFPTDE','vIqaebj-GNw'],
				['backflip-1.jpg','backflip-2.jpg','backflip-3.jpg']
			],
			
		];
        // Tricks
        foreach($tricks as $trick){
            $tr= new Trick();
                $tr->setName($trick[0]);
                $tr->setCategory($this->getReference($trick[1]));
                $tr->setDescription($trick[2]);
				$tr->setUser($this->getReference($trick[3]));

				// Images
				foreach( $trick[5] as $path){
					copy(__DIR__.'/images/'.$path,__DIR__.'/../../public/uploads/trick_images/'.$path);
					$img = new Image();
					$img->setName($path);
					$img->setPath($path);
                	$tr->addImage($img);
				}

				// Videos
				foreach($trick[4] as $embedLinkCode){
					$video = new Video();
					$video->setUrl('https://www.youtube.com/embed/'.$embedLinkCode);
					$tr-> addVideo($video);
				}
				$username=['coco','soso','gogo','dodo','toto','jojo','fofo'];
				// Comments
                for ($u = 0; $u < rand(20, 50); $u++) {
                    // shuffle($users);
                    $comment = new Comment;
                    $comment->setContent($this->faker->unique()->text(150));
                    // $comment->setCreatedAt($this->faker->dateTimeThisDecade());
					$comment->setUser($this->getReference($username[array_rand($username)]));
					
                    // $comment->setUser($users[0]);
                    $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));
                    $tr->addComment($comment);
                }
				$manager->persist($tr);		
		}
        $manager->flush();
    }

	public function getDependencies()
    {
        return [
            UserFixtures::class, CategoryFixtures::class
        ];
    }
}