<?php

namespace App\Form;


use App\Entity\Lieu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
            //lieuVille.VilleSortie.nom

            ->add('nom', TextType::class, [
                'label'=>'Nom du lieu * :',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de choisir un nom pour le lieu'
                    ])
                ]
            ])

            ->add('rue', TextType::class, [
                'label'=>'Rue * :',
                'constraints'=>[
                    new NotBlank([
                        'message'=>"Merci d'indiquer la rue"
                    ])
                ]
            ])

            //lieuVille.codePostal

            ->add('latitude', IntegerType::class, [
                'label'=>'Latitude :',
                'required' => false,
            ])

            ->add('longitude', IntegerType::class, [
                'label'=>'Longitude :',
                'required' => false,
            ])

            ->add('lieuVille', VilleSortieType::class, [
                'label'=>'',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de choisir une ville',
                    ]),
                ]
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
