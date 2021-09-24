<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Lieu;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToValuesTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;


class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Intitulé de la sortie',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de choisir un nom pour la sortie'
                    ])
                ]
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date de la sortie',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner une date valide',
                    ]),
                ]
            ])

            ->add('duree', TimeType::class, [
                'label'=>'Durée de la sortie',
                'required'=>'false',
            ])

            ->add('dateLimiteInscription', DateTimeType::class, [
                'label'=>"Date limite pour s'inscrire à la sortie",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner une date valide',
                    ]),
                ]
            ])

            ->add('nbInscriptionsMax', IntegerType::class, [
                'label'=>"Le nomdre d'inscriptions maximum est de",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner les nombres de personnes pouvant participer à la sortie',
                    ]),
                ]
            ])

            ->add('infosSortie', TextareaType::class, [
                'label'=>'Informations complémentaires sur la sortie',
                'required'=>'false',
            ])

            ->add('sortieLieu', EntityType::class, [
                'label'=>'Lieu de la sortie',
                'class'=>Lieu::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir un lieu',
                    ]),
                ]
                //pour ajouter un lieu si besoin type button
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'method'=> 'POST',
        ]);
    }
}
