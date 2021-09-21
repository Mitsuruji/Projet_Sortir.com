<?php

namespace App\Form;


use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a pseudo',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Your nom should be less than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a pseudo',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Your prenom should be less than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('telephone', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a pseudo',
                    ]),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'Your telephone should be less than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a pseudo',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Your pseudo should be less than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('mail', EmailType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ])
                ]
            ])
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            /*terms validation
             ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
