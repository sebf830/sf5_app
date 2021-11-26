<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_password', RepeatedType::class,  [
                'label' => 'password',
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'required' => true,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['placeholder' => 'Votre mot de passe']
                ],
                'second_options' => [
                    'label' => 'Confirmer le password',
                    'attr' => ['placeholder' => 'Confirmer votre mot de passe']
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
