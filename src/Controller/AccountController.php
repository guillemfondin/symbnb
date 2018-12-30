<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\RegistrationType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;

class AccountController extends AbstractController
{
    /**
     * Affiche et gère le forme de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $utils->getLastUsername()
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     */
    public function logout() {}


    /**
     * Affiche le formulaire d'inscription
     * Permet l'édition du profil avec affichage d'un second formualaire (AccountType)
     * 
     * @Route("/register", name="account_register")
     * @Route("/account/profil", name="account_profil")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
        if ($this->getUser()) {
            $user = $this->getUser();
            $passwordOld = $user->getHash();
            $form = $this->createForm(AccountType::class, $user);
            $modifMode = true;
        } else {
            $user = new User();
            $form = $this->createForm(RegistrationType::class, $user);
            $modifMode = false;
        }
        $error = false;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($modifMode == true) {
                if (!empty($user->getPasswordOld()) && !empty($user->getHash()) && !empty($user->getPasswordConfirm())) {
                    if (password_verify($user->getPasswordOld(), $passwordOld)) {
                        $user->setHash($encoder->encodePassword($user, $user->getHash()));
                    } else {
                        $form->get('passwordOld')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
                        $error = true;
                    }
                } else {
                    $error = true;
                    if (empty($user->getPasswordOld()) && !empty($user->getHash()) || !empty($user->getPasswordConfirm())) {
                        $form->get('passwordOld')->addError(new FormError("Pour modifier le mot de passe, ce champs doit être renseigné"));
                    }
                    if (empty($user->getHash()) && !empty($user->getPasswordOld()) || !empty($user->getPasswordConfirm())) {
                        $form->get('hash')->addError(new FormError("Pour modifier le mot de passe, ce champs doit être renseigné"));
                    }
                    if (empty($user->getPasswordConfirm()) && !empty($user->getHash()) || !empty($user->getPasswordOld())) {
                        $form->get('passwordConfirm')->addError(new FormError("Pour modifier le mot de passe, ce champs doit être renseigné"));
                    }
                }
            } else {
                $user->setHash($encoder->encodePassword($user, $user->getHash()));
            }
            if (!$error) {

                $manager->persist($user);
                $manager->flush();

                if ($modifMode == true) {
                    $this->addFlash(
                        'success',
                        "Les modifications ont bien été enregistrées"
                    );

                    return $this->redirectToRoute('account_index');
                } else {
                    $this->addFlash(
                        'success',
                        "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter"
                    );

                    return $this->redirectToRoute('account_login');
                }
            } else {
                $user->setHash($passwordOld);
            }
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
            'modifMode' => $modifMode
        ]);
    }


    /**
     * Affiche le profil de l'utilisateur connedté
     *
     * @Route("/account", name="account_index")
     * 
     * @return Response
     */
    public function myAccount() {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
