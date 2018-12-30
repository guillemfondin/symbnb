<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfig("Prénom", "Votre prénom"))
            ->add('lastName', TextType::class, $this->getConfig("Nom", "Votre nom"))
            ->add('email', EmailType::class, $this->getConfig("Email", "Votre adresse email"))
            ->add('picture', UrlType::class, $this->getConfig("Avatar", "Url de votre photo de profil"))
            ->add('hash', PasswordType::class, $this->getConfig("Mot de passe", "Mot de passe complexe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfig("Confirmation du mot de passe", "Confirmez votre mot de passe"))
            ->add('introduction', TextType::class, $this->getConfig("Introduction", "Une courte description de vous"))
            ->add('description', TextareaType::class, $this->getConfig("Présentation détaillée", "Pésentez vous en détail"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
