<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnulerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motifAnnulation', TextareaType::class, [
                'label' => 'Motif: ',
                'required' => true,
                'attr' => ['placeholder' => '250 caractères max'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a valid text',
                    ]),
                    new Length([
                        'max' => 250,
                        'maxMessage' => 'Your nom should be less than {{ limit }} characters'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'method' => 'POST'
        ]);
    }

}