<?php

namespace App\Form;


use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom* ',
                'required' => true,
                'attr' => ['placeholder' => '50 caractères max'],
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
                'label' => 'Prénom* ',
                'required' => true,
                'attr' => ['placeholder' => '50 caractères max'],
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
                'label' => 'Telephone* ',
                'required' => true,
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
                'label' => 'Pseudo*',
                'required' => true,
                'attr' => ['placeholder' => '50 caractères max'],
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
                'label' => 'Mail* ',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ])
                ]
            ])
            ->add('campus', EntityType::class,[
                'label' => 'Campus* ',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe*:'],
                'second_options' => ['label' => 'Repeter mot de passe*:'],
                'mapped' => false,
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
                ]
            ])
            ->add('photo', FileType::class,[
                'label' => 'Photo (jpg)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k'
                    ])
                ]
            ])
            ->add('administrateur', CheckboxType::class,[
                'label' => 'admin',
                'required' => false,
            ])
            ->add('actif', CheckboxType::class,[
                'label' => 'actif',
                'required' => false,
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
