<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 200,
                        'maxMessage' => "La taille max est de est 200 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'label' => 'Prénom',
                    'class' => 'form-control'
                ]
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 200,
                        'maxMessage' => "La taille max est de est 200 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'label' => 'Nom',
                    'class' => 'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 200,
                        'maxMessage' => "La taille max est de est 200 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'label' => 'Ville',
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 10,
                        'maxMessage' => "La taille max est de est 10 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'label' => 'Téléphone',
                    'class' => 'form-control'
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Choisir un avatar (optionnel)',
                'required' => false,
                'multiple' => false
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 200,
                        'maxMessage' => "La taille max de l'email est 200 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'label' => 'Email',
                    'class' => 'form-control'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'label' => 'Password',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Entrer un mot de passe',]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez les conditions du site',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'enregistrer',
                'attr' => [
                    'class' => 'btn btn-success',
                    'formnovalidate' => 'formnovalidate'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
