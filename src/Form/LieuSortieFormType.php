<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom du lieu de la sortie',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de choisir un nom pour le lieu'
                    ])
                ]
            ])

            ->add('rue', TextareaType::class, [
                'label'=>'Rue',
                'constraints'=>[
                    new NotBlank([
                        'message'=>"Merci d'indiquer la rue"
                    ])
                ]
            ])

            ->add('latitude', IntegerType::class, [
                'label'=>'Latitude',
            ])

            ->add('longitude', IntegerType::class, [
                'label'=>'longitude',
            ])

            ->add('lieuVille', TextType::class, [
                'label'=>'Ville',
                'constraints'=>[
                    new NotBlank([
                        'message'=>"Merci d'indiquer la ville"
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
