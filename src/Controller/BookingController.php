<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Faker\Provider\cs_CZ\DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Comment;
use App\Form\CommentType;

class BookingController extends AbstractController
{
    /**
     * Permet la réservation d'une location
     * 
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setBooker($this->getUser())
                    ->setAd($ad)
            ;

            // Si date indisponible
            if (!$booking->isBookableDate()) {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisi ne sont pas disponible"
                );
            } else {
                $manager->persist($booking);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    "<h2>Félicitations !</h2><p>Votre réservation a bien été enregistrée !</p>"
                );
                return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
            }

        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche une page de résa
     *
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager) {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser())
                    ->setBooking($booking)
            ;
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "<h2>Merci pour votre précieux avis !</h2><p>Votre commentaire a bien été enregistré</p>"
            );
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
