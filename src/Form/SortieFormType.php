<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'label'=>'Description et informations complémentaires sur la sortie :',
                'required'=>'false',
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

            ->add('ville', EntityType::class, [
                'class'       => 'App\Entity\Ville',
                'placeholder' => 'Sélectionnez votre ville',
                'mapped'      => false,
                'required'    => false
            ]);


            $builder->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addLieuField($form->getParent(), $form->getData());
                }
            );

            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $data = $event->getData();
                    /* @var $lieu Lieu */
                    $lieu = $data->getSortieLieu();
                    $form = $event->getForm();
                    if ($lieu) {
                        // On récupère le département et la région
                        $ville = $lieu->getLieuVille();
                        // On crée le champs supplémentaires
                        $this->addLieuField($form, $ville);;
                        // On set les données
                        $form->get('ville')->setData($ville);
                        $form->get('lieu')->setData($lieu);
                    } else {
                        // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
                        $this->addLieuField($form, null);
                    }
                }
            );
            /*->add('sortieLieu', LieuSortieFormType::class, [
                'label'=>false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir un lieu',
                    ]),
                ]
            ])*/
        ;
    }

    private function addLieuField(FormInterface $form, Ville $ville)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'lieu',
            EntityType::class,
            null,
            [
                'class'           => 'App\Entity\Ville',
                'placeholder'     => $ville ? 'Sélectionnez votre lieu' : 'Sélectionnez votre ville',
                'mapped'          => false,
                'required'        => false,
                'auto_initialize' => false,
                'choices'         => $ville ? $ville->getVilleLieux() : []
            ]
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
