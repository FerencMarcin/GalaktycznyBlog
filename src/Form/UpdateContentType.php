<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', FileType::class, [
                'label' => ' ',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Przesłany plik musi być w formacie obrazu'
                    ])
                ]
            ])
            ->add('is_public', CheckboxType::class, [
                'label' => 'Publiczne',
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => ' ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Musisz podać tytuł przed dodaniem wpisu',
                    ]),
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Tytuł Twojego wpisu może mieć maksymalnie {{ limit }} znaków',
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => ' ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Twój wpis musi zawierać treść',
                    ]),
                    new Length([
                        'max' => 4000,
                        'maxMessage' => 'Twój wpis może zawierać maksymalnie {{ limit }} znaków',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
