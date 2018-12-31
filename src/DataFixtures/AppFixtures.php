<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Guillem')
                  ->setLastName('Fondin')
                  ->setEmail('g.fondin@agence-massai.fr')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://avatars.io/facebook/GuillemFondin')
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>'.join($faker->paragraphs(3),'</p><p>').'</p>')
                  ->addUserRole($adminRole)
        ;
        $manager->persist($adminUser);

        //Génération des users
        $users = [];
        $genres = ['male', 'female'];
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence)
                 ->setDescription('<p>'.join($faker->paragraphs(3),'</p><p>').'</p>')
                 ->setHash($this->encoder->encodePassword($user, 'password'))
                 ->setPicture('https://randomuser.me/api/portraits/'.($genre == 'male' ? 'men/' : 'women/').mt_rand(0,99).'.jpg')
            ;
            $manager->persist($user);
            $users[] = $user;
        }

        //Génération des annonces
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();
            $ad->setTitle($faker->sentence())
               ->setCoverImage($faker->imageUrl(1000,350))
               ->setIntroduction($faker->paragraph(2))
               ->setContent('<p>'.join($faker->paragraphs(5),'</p><p>').'</p>')
               ->setPrice(mt_rand(40,200))
               ->setRooms(mt_rand(1,7))
               ->setAuthor($faker->randomElement($users))
            ;

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad)
                ;
                $manager->persist($image);
            }
            
            //Génération des résas
            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $booking = new Booking();

                $duration = mt_rand(3,10);
                $startDate = $faker->dateTimeBetween("-3 mounths");
                $endDate = (clone $startDate)->modify(("+$duration days"));
                $amount = $ad->getPrice() * $duration;
                $booker = $faker->randomElement($users);

                $booking->setCreatedAt($faker->dateTimeBetween("-6 mounths"))
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setAmount($amount)
                        ->setBooker($booker)
                        ->setAd($ad)
                        ->setComment($faker->paragraph())
                ;

                $manager->persist($booking);

                // génération des commentaires
                if (mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                            ->setRating(mt_rand(1, 5))
                            ->setAd($ad)
                            ->setAuthor($booker)
                    ;
                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}
