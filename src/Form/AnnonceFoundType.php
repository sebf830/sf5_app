<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceFoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 200,
                        'maxMessage' => "La taille max est de est 200 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ]
            ])
            ->add('race', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ],
                'choices' => [
                    "Choisir" => null,
                    "femelle" => "femelle",
                    "male" =>  "male",
                ]
            ])
            ->add('color', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ],
                'choices' => [
                    "Choisir" => null,
                    'brun' => 'brun',
                    'blanc' => 'blanc',
                    'creme' => 'creme',
                    'gris' => 'gris',
                    'roux' => 'roux',
                    'marron clair' => 'marron clair',
                    'tricolore' => 'tricolore',
                    'bicolore' => 'bicolore',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => "Veuillez raccourcir votre message"
                    ])
                ],
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ],
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'required' => false,
                'multiple' => false,
                'attr' => [
                    'placeholder' => 'image',
                    'class' => 'form-control mt-4 py-0 px-2'
                ],
            ])
            ->add('foundAt', DateType::class, [
                'label' => false,
                'required' => false,
                'format' => 'yyyy-MM-dd',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control mt-4 py-0 px-2'
                ],
            ])
            ->add('location', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'class' => 'form-control mt-4 py-0 px-2'
                ],
            ])
            ->add('isLost', HiddenType::class, [
                'label' => false,
                'empty_data' => 0,
                'attr' => [
                    'style' => 'display:none'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier votre annonce maintenant',
                'attr' => [
                    'class' => 'btn btn-secondary w-100 mt-3',
                    'formnovalidate' => 'formnovalidate'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
