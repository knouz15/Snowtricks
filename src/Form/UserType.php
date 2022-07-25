<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '50',
                ],
                'label' => 'Username',
                'label_attr' => [
                    'class' => 'form-label  mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])


            ->add('plainPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Mot de passe',
                'label_attr' => [
                    'class' => 'form-label  mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    // new Assert\Email(),
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Votre Avatar',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false
            ])

            // ->add('avatar', FileType::class, [
            //     'label' => 'Votre photo',
                 
            //     // unmapped means that this field is not associated to any entity property
            //     //'mapped' => false,
 
            //     // make it optional so you don't have to re-upload the PDF file
            //     // every time you edit the Product details
            //     'required' => false,
 
            //     // unmapped fields can't define their validation using annotations
            //     // in the associated entity, so you can use the PHP constraint classes
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '1024k',
            //             'mimeTypes' => [
            //                 'image/jpeg',  
            //                 'image/jpg',
            //             ],
            //             'mimeTypesMessage' => 'Merci d\'uploader une image au format jpg ou jpeg',
            //         ])
            //     ],
            // ])



            
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
            'data_class' => User::class,
        ]);
    }
}
