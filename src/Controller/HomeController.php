<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AdRepository;
use App\Repository\UserRepository;

class HomeController extends AbstractController{
    /**
     * Home page
     * @Route("/", name="homepage")
     * @return response
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo) {
        return $this->render('home.html.twig', [
            'ads' => $adRepo->findBestAds(),
            'users' => $userRepo->findBestUsers()
        ]);
    }
}