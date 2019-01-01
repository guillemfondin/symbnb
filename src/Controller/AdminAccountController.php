<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $utils->getLastUsername()
        ]);
    }

    /**
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout() {}
}
