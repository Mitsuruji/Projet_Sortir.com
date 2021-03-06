<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LieuSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use($options) {
        $example = $event->getData(); //recuperation de l'objet sur lequel le formulaire se base
        $form = $event->getForm(); //recuperation du formulaire
        //en fonction du type de l'utilisateur les champs sont différents
        if ($example->getStatus() == 1 && $options['custom']) {
        $form->add('firstName', null, array('label' => 'Prénom'));
        //....
        } else {
        $form->add('lastName', null, array('label' => 'Nom'));
        //....
        }
         */
        $builder

            ->add('nom', EntityType::class, [
                'label'=>'Nom du lieu * :',
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de choisir un nom pour le lieu'
                    ])
                ]
            ])

            ->add('latitude', IntegerType::class, [
                'label'=>'Latitude :',
                'required' => false,
            ])

            ->add('longitude', IntegerType::class, [
                'label'=>'Longitude :',
                'required' => false,
            ])

            ->add('lieuVille', EntityType::class, [
                'label'=>false,
                'class' => 'App\Entity\Ville',
                'placeholder' => 'Sélectionnez votre ville',
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une ville',
                    ]),
                ]
            ]);}
//
//        $builder->get('lieuVille')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) {
//                $form = $event->getForm();
//                $this->addVilleField($form->getParent(), $form->getData());
//            }
//        );
//
//        $builder->addEventListener(
//            FormEvents::POST_SET_DATA,
//            function (FormEvent $event) {
//                $data = $event->getData();
//                /* @var $ville Ville */
//                $ville = $data->getLieuVille();
//                $form = $event->getForm();
//                if ($ville) {
//                    // On récupère le département et la région
//                    $lieu = $ville->getVilleLieux();
//                    // On crée le champs supplémentaires
//                    $this->addVilleField($form, $ville);;
//                    // On set les données
//                    $form->get('ville')->setData($ville);
//                    $form->get('villelieux')->setData($lieu);
//                } else {
//                    // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
//                    $this->addVilleField($form, null);
//                }
//            }
//        );
//    }
//
//    private function addVilleField(FormInterface $form,?Ville $ville)
//    {
//        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
//            'villeLieu',
//            EntityType::class,
//            null,
//            [
//                'class'           => 'App\Entity\Ville',
//                'placeholder'     => $ville ? 'Sélectionnez votre lieu' : 'Sélectionnez votre ville',
//                'mapped'          => false,
//                'required'        => false,
//                'auto_initialize' => false,
//                'choices'         => $ville ? $ville->getVilleLieux() : []
//            ]
//        );
//    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
            'method'=> 'POST',
        ]);
    }
}
