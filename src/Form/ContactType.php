<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez inserer 2 caractères minimum',
                        'max' => 100,
                        'maxMessage' => 'Le prénom doit comporter 100 caractères maximum',
                    ]),
                ],
            ])
            ->add('lastname',  TextType::class, [
                'label' => 'Nom : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Veuillez inserer 2 caractères minimum',
                        'max' => 100,
                        'maxMessage' => 'Le nom doit comporter 100 caractères maximum',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'email : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Email([
                        'message' => 'Veuillez renseigner un email valide'
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Veuillez inserer 2 caractères minimum',
                        'max' => 500,
                        'maxMessage' => 'Le message doit comporter 500 caractères maximum',
                    ]),
                ],
                'attr' => [
                    'style' => 'height:120px;'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'valider',
                'attr' => [
                    'formnovalidate' => 'formnovalidate',
                    'class' => 'btn item-green border'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
