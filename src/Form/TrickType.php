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
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir le nom du trick ',
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
                    // 'min' => 1,
                    // 'max' => 5
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
            ->add('category', EntityType::class, [ 
                'class' => Category::class,
                'choice_label' => 'slug',
                'label' => 'Catégorie',
                'placeholder' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir une categorie',
                    ])
                ]
            ])
            //   On ajoute le champ "images" dans le formulaire. Il n'est pas lié à la base de données (mapped à false)
             ->add('images', FileType::class, [
                 'label' => 'Images: par exemple jpg ou png  ',
                 'multiple' => true,
                 'mapped' => false,
                 'required' => false
             ]) 
            
            ->add('tags', CollectionType::class, [
                
                'entry_type' => TagType::class, 
                
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                
                // 'label' => 'Vos vidéos',
                // 'mapped' => false,
                
                
                'required' => true,
               
            ]);

    } 

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
