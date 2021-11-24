<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Renseignez un titre',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 150,
                        'maxMessage' => "La taille max est de est 150 caractères"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Rédigez votre message',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'max' => 5000,
                        'maxMessage' => "Veuillez raccourcir votre contenu"
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control m-0 py-0 px-2'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Choisir une categorie',
                'required' => true,
                'class' => Category::class,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une categorie']),
                ],
                'query_builder' => function (CategoryRepository $repo) {
                    return $repo->createQueryBuilder('c')->where('c.name != :bind')->setParameter('bind', 'temoignages')->orderBy('c.name', 'ASC');
                },
                'expanded' => true
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une image']),
                ],
                'attr' => [
                    'placeholder' => 'image',
                    'class' => 'form-control mt-4 py-0 px-2'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'modifier',
                'attr' => [
                    'class' => 'btn btn-secondary',
                    'formnovalidate' => 'formnovalidate'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
