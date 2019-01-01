<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\AdminBookingType;
use App\Service\Paginator;

class AdminBookingController extends AbstractController
{
    /**
     * Affiche la liste des réservations
     * 
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     * 
     * @param BookingRepository $repo
     * 
     * @return Response
     */
    public function index(BookingRepository $repo, $page, Paginator $paginator) {
        $paginator->setEntityClass(Booking::class)
                  ->setCurrentPage($page)
        ;
        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $paginator
        ]);
    }

    /**
     * Editer une résa
     * 
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit") 
     * 
     * @return Response
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                'La réservation a bien été modifiée'
            );
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Supprime une résa
     *
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     * 
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager) {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            'La réservation a bien été suprimée'
        );
        return $this->redirectToRoute('admin_bookings_index');
    }
}
