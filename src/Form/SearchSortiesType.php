<?php

namespace App\Form;

use App\Data\SearchOptions;
use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choixCampus', EntityType::class,
                [
                'label' => 'Campus: ',
                'required' => false,
                'class' => Campus::class,
                'choice_label' => 'nom',
                ])

            ->add('filterNomSortie', TextType::class,
                [
                'label' => 'Le nom de la sortie contient: ',
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher une sortie']
                ])

            ->add('filterDateMin', DateType::class,
                [
                   'label' => 'Entre: ',
                    'required' => false,
                    'widget' => 'single_text'
                ])
            ->add('filterDateMax', DateType::class,
                [
                    'label' => 'et: ',
                    'required' => false,
                    'widget' => 'single_text'
                ])
            ->add('filterIsOrganisateur', CheckboxType::class,
                [
                  'label' => 'Sorties dont je suis l\'organisateur/trice',
                    'required' => false,
                ])
            ->add('filterIsInscris', CheckboxType::class,
                [
                    'label' => 'Sorties auxquelles je suis inscrit/e',
                    'required' => false,
                ])
            ->add('filterIsPasInscris', CheckboxType::class,
                [
                    'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                    'required' => false,
                ])
            ->add('filterSortiesPassees', CheckboxType::class,
                [
                    'label' => 'Sorties passées',
                    'required' => false,
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchOptions::class,
            'method' => 'GET'
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
