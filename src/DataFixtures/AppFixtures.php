<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 30; $i++) {
            $faker = Factory::create('fr-FR');
            $ad = new Ad();
            $ad->setTitle($faker->sentence())
               ->setCoverImage($faker->imageUrl(1000,350))
               ->setIntroduction($faker->paragraph(2))
               ->setContent('<p>'.join($faker->paragraphs(2),'</p><p>').'</p>')
               ->setPrice(mt_rand(40,200))
               ->setRooms(mt_rand(1,7))
            ;

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad)
                ;
                $manager->persist($image);
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}
