<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\AnnonceType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Image;

class AdController extends AbstractController
{
    /**
     * Affiche toutes les annonces
     * 
     * @Route("/ads", name="ads_index")
     * 
     * @return Response
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     *
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager) {
        $ad = new Ad();
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce a bien été enregistrée"
            );
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche le formulaire d'édition
     *
     * @Route("/add/{slug}/edit", name=""ads_edit")
     * 
     * @return Response
     */
    public function edit() {
        return $this->render('ad/edit.html.twig');
    }

    /**
     * Affiche une annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad) {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }
}
