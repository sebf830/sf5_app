<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Sujet : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Veuillez inserer 3 caractères minimum',
                        'max' => 100,
                        'maxMessage' => 'Le sujet doit comporter 100 caractères maximum',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Indiquer un sujet'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Veuillez inserer 10 caractères minimum',
                        'max' => 100,
                        'maxMessage' => 'Le sujet doit comporter 100 caractères maximum',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Indiquer un sujet'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'envoyer',
                'attr' => [
                    'formnovalidate' => 'formnovalidate',
                    'class' => 'btn item-green border valid_message'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
