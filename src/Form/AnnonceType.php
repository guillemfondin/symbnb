<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends AbstractType
{
    
    /**
     * Permet d'avoir la config de base d'un champs
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfig($label, $placeholder, $options = []) {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfig("Titre", "Titre de l'annonce"))
            ->add('slug', TextType::class, $this->getConfig("Slug", "Adresse web de l'annonce (automatique)", ['required' => false]))
            ->add('coverImage', UrlType::class, $this->getConfig("Url de l'image principale", "L'url de l'image principale de votre annonce"))
            ->add('introduction', TextType::class, $this->getConfig("Introduction", "Courte description de votre annonce"))
            ->add('content', TextareaType::class, $this->getConfig("Description détaillée", "Description détaillée de votre annonce"))
            ->add('rooms', IntegerType::class, $this->getConfig("Nombre de pièces", "Nombre de pièces de votre bien"))
            ->add('price', MoneyType::class, $this->getConfig("Prix par nuit", "Indiquez le prix par nuit"))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Images'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
