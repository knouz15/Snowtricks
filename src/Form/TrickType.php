<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Category;
use App\Form\VideoType; 
use App\Form\ApplicationType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    "placeholder" => "Entrez un nom",
                    'minlenght' => '3',
                    'maxlenght' => '50',

                ],
                'label' => 'Nom trick',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir le nom du trick ',
                    ]),
                    new Length(['min' => 3, 'max' => 50, 'minMessage' => 'Le nom du trick doit contenir au moins 3 caractères'
                    ])
                ]
            ])
            // ->add('slug', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control'
            //     ],
            //     'label' => 'Slug',
            //     'label_attr' => [
            //         'class' => 'form-label mt-4'
            //     ],
            //     'constraints' => [
            //         new Assert\NotBlank()
            //     ]
            // ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    "placeholder" => "Décrivez le trick ..."
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir une description du trick',
                    ])
                ]
                ])
                ->add('videos', CollectionType::class, [
                    'entry_type'     => VideoType::class,
                    'entry_options'  => [
                        'label' => false,
                    ],
                    'by_reference'   => false,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                    'required'       => false,
                    'prototype'      => true,
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Veuillez renseigner les médias',
                        ]),
                    ]
                ])
                ->add('imagesFile', CollectionType::class, [
                    'entry_type'     => FileType::class,
                    'entry_options'  => [
                        'label' => false,
                    ],
                    'by_reference'   => false,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                    'mapped' => false,
                    'required'       => false,
                    'prototype'      => true,
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Veuillez renseigner les médias',
                        ]),
                    ]
                ])

                // ->add('images', FileType::class, [
                //     'label' => 'image (PDF file)',
                //     // 'multiple' => true,
    
                //     // unmapped means that this field is not associated to any entity property
                //     'mapped' => false,
    
                //     // make it optional so you don't have to re-upload the PDF file
                //     // every time you edit the Product details
                //     'required' => false,
    
                //     // unmapped fields can't define their validation using annotations
                //     // in the associated entity, so you can use the PHP constraint classes
                //     'constraints' => [
                //         new File([
                //             'maxSize' => '1024k',
                //             'mimeTypes' => [
                //                 'application/jpg',
                //                 'application/png',
                //             ],
                //             'mimeTypesMessage' => 'Uploadez une image à formatvalide',
                //         ])
                //     ],
                // ])

                // ->add('images', FileType::class, [
                //     'label' => 'Images:jpg ou png ou  ',
                //     'multiple' => true,
                //     'mapped' => false,
                //     'required' => true
                // ]) 
            ->add('category', EntityType::class, [ 
                'class' => Category::class,
                // 'choice_label' => 'slug',
                'label' => 'Catégorie',
                'placeholder' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir une catégorie',
                    ])
                ] 
                    ]);
           
    } 

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
