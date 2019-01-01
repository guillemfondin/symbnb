<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\AnnonceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminBookingType extends AnnonceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, $this->getConfig("Date de départ", "", ['widget' => 'single_text']))
            ->add('endDate', DateType::class, $this->getConfig("Date d'arrivée", "", ['widget' => 'single_text']))
            ->add('comment', TextareaType::class, $this->getConfig("Commentaire", "Ici, les demandes supplémentaires"))
            ->add('booker', EntityType::class, $this->getConfig("Locataire","",[
                'class' => User::class,
                'choice_label' => 'fullName'
            ]))
            ->add('ad', EntityType::class, $this->getConfig("Annonce","",[
                'class' => Ad::class,
                'choice_label' => 'title'
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
