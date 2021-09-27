<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'label'=>'Nom du lieu',
                /*'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de choisir un nom pour le lieu'
                    ])
                ]*/
            ])

            ->add('rue', TextType::class, [
                'label'=>'Rue',
                /*'constraints'=>[
                    new NotBlank([
                        'message'=>"Merci d'indiquer la rue"
                    ])
                ]*/
            ])

            ->add('latitude', IntegerType::class, [
                'label'=>'latitude',
                'required' => false,
            ])

            ->add('longitude', IntegerType::class, [
                'label'=>'longitude',
                'required' => false,
            ])

            ->add('lieuVille', EntityType::class, [
                'label'=>'Ville',
                'class'=>Lieu::class,
                'choice_label'=>''
                /*'constraints'=>[
                    new NotBlank([
                        'message'=>"Merci d'indiquer la ville"
                    ])
                ]*/
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
            'method'=> 'POST',
        ]);
    }
}
