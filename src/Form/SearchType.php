<?php

namespace App\Form;

use App\Class\Search;
use App\Form\Extension\RaceType;
use Symfony\Component\Form\AbstractType;
use App\Serializer\csv\AnimalRaceSerializer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;

class SearchType extends AbstractType
{

    private AnimalRaceSerializer $raceSerializer;

    public function __construct(AnimalRaceSerializer $raceSerializer)
    {
        $this->raceSerializer = $raceSerializer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de recherche',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'placeholder' => 'Que cherchez vous ?',
                    'class' => 'form-control'
                ],
                'choices' => [
                    "choisir" => null,
                    "J'ai perdu mon chien" => 1,
                    "J'ai perdu mon chat" => 2,
                    "J'ai trouvé un chien" => 3,
                    "J'ai trouvé un chat" => 4,
                ]
            ])
            ->add('date', DateType::class, [
                'label' => "Perdu / trouvé le (optionnel) : ",
                'required' => false,
                'format' => 'yyyy-MM-dd',
                'html5' => false,
                // 'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Indiquer une date',
                    'class' => 'form-control js-datepicker'
                ],
            ])
            ->add('city', TextType::class, [
                'label' => "Ville :",
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'placeholder' => 'Indiquer une ville',
                    'class' => 'form-control'
                ],
            ])
            ->add('race', TextType::class, [
                'label' => "Race :",
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'placeholder' => 'Indiquer une race',
                    'class' => 'form-control'
                ],
            ])

            ->add('gender', ChoiceType::class, [
                'label' => "Sexe :",
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'placeholder' => 'Male ou femelle',
                    'class' => 'form-control'
                ],
                'choices' => [
                    "Choisir" => null,
                    "femelle" => "femelle",
                    "male" =>  "male",
                ]
            ])

            ->add('color', ChoiceType::class, [
                'label' => "Couleur :",
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez remplir le champs'])
                ],
                'attr' => [
                    'placeholder' => 'Indiquer une couleur',
                    'class' => 'form-control'
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
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-secondary w-100 mt-3',
                    'formnovalidate' => 'formnovalidate'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'csrf_protection' => false
        ]);
    }
}
