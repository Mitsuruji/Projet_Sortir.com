<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModifierSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom de la sortie * :',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de choisir un nom pour la sortie'
                    ])
                ]
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date et heure de la sortie * :',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner une date valide',
                    ]),
                ]
            ])

            ->add('dateLimiteInscription', DateType::class, [
                'label'=>'Date limite d\'inscription * :',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner une date valide',
                    ]),
                ]
            ])

            ->add('nbInscriptionsMax', IntegerType::class, [
                'label'=>"Nombres de places * :",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner les nombres de personnes pouvant participer à la sortie',
                    ]),
                ]
            ])

            ->add('duree', TimeType::class, [
                'label'=>'Durée de la sortie * :',
                'required'=>false,
                'widget' => 'single_text',
            ])

            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et informations complémentaires sur la sortie *:',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner ce champ',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Merci de décrire plus amplement la sortie',
                        'max' => 300,
                    ]),
                ]
            ])

            ->add('campusOrganisateur', EntityType::class, [
                'label'=>'Campus * :',
                'class'=>Campus::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir un campus',
                    ]),
                ]
            ])

            ->add('sortieLieu', LieuSortieFormType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir un lieu',
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'method' => 'GET'
        ]);
    }
}
