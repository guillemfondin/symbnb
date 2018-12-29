<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller{
    /**
     * Home page
     * @Route("/", name="homepage")
     * @return response
     */
    public function home() {
        return $this->render('home.html.twig', [
            'title' => 'Bonjour à tous',
            'age' => 31
        ]);
    }
}