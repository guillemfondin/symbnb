<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class AdminAdController extends AbstractController
{
    /**
     * Affiche l'ensemble des annonces
     * 
     * @Route("/admin/ads", name="admin_ads_index")
     * 
     * @param AdRepository $AdRepository
     * @return Response
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findAll(),
        ]);
    }

    /**
     * Permet l'édition d'une annonce
     * 
     * @Route("/admin/ad/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce a bien été modifiée"
            );
            // return $this->redirectToRoute('admin_ads_index');
        }
        return $this->render('admin/ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Supprime une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")     * 
     * 
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return void
     */
    public function delete(Ad $ad, ObjectManager $manager) {
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute("admin_ads_index");
    }
}
