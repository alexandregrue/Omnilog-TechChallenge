<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom :',
                'attr' => [
                    'placeholder' => 'Nom du projet',
                ],
                
            ])
            ->add('text', TextType::class, [
                'label' => 'Description :',
                'attr' => [
                    'placeholder' => 'Description du projet',
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Début :',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'invalid_message' => 'La date doit être sous la forme JJ/MM/AAAA',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Fin :',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'invalid_message' => 'La date doit être sous la forme JJ/MM/AAAA',
            ])
            ->add('documents', FileType::class, [
                'label' => 'Documents :',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        new File([
                            'maxSize' => '2M',
                            'uploadIniSizeErrorMessage' => 'Le fichier doit faire moins de 2Mo.',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg',
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'mimeTypesMessage' => 'Merci de télécharger un fichier au format PNG, JPG, JPEG ou PDF.',
                        ]),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
