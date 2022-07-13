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
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    // 'min' => 1,
                    // 'max' => 5
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
                ])
            ->add('category', EntityType::class, [ 
                'class' => Category::class,
                'choice_label' => 'slug',
                'label' => 'Catégorie',
            ])
             // On ajoute le champ "images" dans le formulaire
            // Il n'est pas lié à la base de données (mapped à false)
            ->add('images', FileType::class, [
                'label' => 'Images:jpg ou png ou  ',
                'multiple' => true,
                'mapped' => false,
                'required' => true
            ]) 
            // ->add('videos', FileType::class, [
            //     'label' => 'Uploader Vidéos sous format mp4',
            //     'multiple' => true,
            //     'mapped' => false,
            //     'required' => true
            // ])
            // ->add('images', CollectionType::class, [
                // 'entry_type' => ImageType::class, 
                // 'label' => 'Vos images',
                // 'entry_options' => ['label' => false],
                // 'allow_add' => true,
                // 'by_reference' => false,
                // 'allow_delete' => true,
                // 'mapped' => false,
                // 'required' => true,
                // 'attr' => [
                //     'class' => 'col-12'
                // ]
                
            // ])

            // ->add('videos', CollectionType::class, [
            //     // each entry in the array will be an "email" field
            //     'entry_type' => TextType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'prototype' => true,
            //     'prototype_data' => "URL d'une video youtube",
            //     // these options are passed to each "email" type
            //     'entry_options' => [
            //         'attr' => ['class' => 'videos-box'],
            //     ],
    

            ->add('tags', CollectionType::class, [
                
                'entry_type' => TagType::class, 
                
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                
                // 'label' => 'Vos vidéos',
                // 'mapped' => false,
                
                
                'required' => true,
                // 'prototype' => true,
                // 'help_html' => true,
                // 'attr' => [
                //     'class' => 'col-12'
                // ]
                // 'entry_type' => VideoType::class,
                // 'entry_options' => ['label' => true],
                // 'allow_add' => true,
                // 'allow_delete' => true,
                // 'by_reference' => false,
                // 'label' => 'Iframe de vidéo',
                // 'label_attr' => [
                //     'class' => 'form-label mt-4'
                // ],
                // 'required' => true
                ])

            // ->add('videos', EntityType::class, [
            //     'label' => 'Selectionner vos videos :',
            //     'class' => Video::class,
            //     'choice_label' => 'name',
            //     'multiple' => true,
            //     'expanded' => true,
            //     'required' => false,
            // ])  



            // Manage submit label
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
        //     $form = $event->getForm();
        //     $data = $event->getData();

        //     if (null === $data) {
        //         return;
        //     }

        //     $form
                // ->add('save', SubmitType::class, [
                //     'label' => $data->getId() ? 'Edit' : 'Create',
                //     'attr' => [
                //         'class' => 'btn-primary btn-lg btn-block',
                //     ],
                // ]);
        // }
    

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Soumettre' 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
