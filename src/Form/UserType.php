<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserType extends AbstractType
{

    public function __construct(TokenStorageInterface $security)
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $avatar = $this->security->getToken()->getUser()->getAvatar();

        $builder
            ->add('email', EmailType::class, [
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
                    'class' => 'form-control py-0 px-2'
                ]
            ])
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
                    'class' => 'form-control py-0 px-2'
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
                    'class' => 'form-control py-0 px-2'
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
                    'class' => 'form-control py-0 px-2'
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Choisir un avatar (optionnel)',
                'required' => false,
                'multiple' => false,
                'data_class' => null,
                'empty_data' => $avatar,
                'attr' => [
                    'class' => 'form-control mt-4 py-0 px-2',
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
