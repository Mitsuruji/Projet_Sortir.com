<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class SortieFormType extends AbstractType
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

            ->add('dateLimiteInscription', DateTimeType::class, [
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
                'widget' => 'single_text',
            ])

            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et informations complémentaires sur la sortie :',
                'required' => false
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


            ->add('sortieVille', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label'=> 'Ville*: ',
                'placeholder' => 'Choisir une ville',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une ville',
                    ]),
                ]
            ])

            ->add('sortieLieu', ChoiceType::class, [
                'label' => 'Lieu*: ',
                'placeholder' => 'Choisir une ville'
            ]);

        $formModifier = function (FormInterface $form, ?Ville $ville = null){
            $lieu = (null === $ville) ? [] : $ville->getVilleLieux();

            $form->add('sortieLieu', EntityType::class, [
                'class' => Lieu::class,
                'choices' => $lieu,
                'choice_label' => 'nom',
                'label' => 'Lieu*: ',
                'placeholder' => 'Choisir un lieu'
            ]);
        };

        $builder->get('sortieVille')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier){
                $form = $event->getForm();
                $formModifier($form->getParent(),$form->getData());
            }
    );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'method'=> 'POST',
        ]);
    }
}
