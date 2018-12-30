<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdController extends AbstractController
{
    /**
     * Permet la validation et le formattage des données envoyées
     *
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @param string $attribut
     * @return Response
     */
    private function validator($ad, $request, $manager, $attribut) {
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce a bien été $attribut"
            );
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $ad->getId() !== null
        ]);
    }


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
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function new(Request $request, ObjectManager $manager) {
        $ad = new Ad();
        return $this->validator($ad, $request, $manager, 'enregistrée');
        // return $this->render('ad/form.html.twig', [
        //     'form' => $form->createView(),
        //     'editMode' => null
        // ]);
    }

    /**
     * Affiche le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas !")
     * 
     * @return Response
     */
    public function create(Ad $ad, Request $request, ObjectManager $manager) {
        return $this->validator($ad, $request, $manager, 'modifiée');
        // return $this->render('ad/form.html.twig', [
        //     'form' => $form->createView(),
        //     'editMode' => true,
        // ]);
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

    /**
     * Supprime une annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas !")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager) {
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute("ads_index");
    }
}
