<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            // ->add('username', TextType::class, [
            //     'attr' => [
            //     'class' => 'form-control',
            //     "placeholder" => "Entrez votre username",
            //     'label' => 'Votre nom d\'utilisateur',
            //     // 'minlenght' => '2',
            //     // 'maxlenght' => '50',
            //     // ],
            //     // 'label_attr' => [
            //     // 'class' => 'form-label  mt-4'
            //     // ],
            //     // 'constraints' => [
            //     //     new NotBlank([
            //     //         'message' => 'Veuillez saisir votre username',
            //     //     ])
            //     ]
            // ])
            // ->add('password', PasswordType::class, [ 
            //     'attr' => ['class' => 'form-control'],
            //     'label' => 'Nouveau mot de passe',
            //     'label_attr' => ['class' => 'form-label mt-4'],
            //     'constraints' => [
            //         new Assert\NotBlank([
            //         'message' => 'Veuillez saisir un mot de passe',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Votre mot de passe doit contenir plus de 7 caractères',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //             // 'message' => 'Votre password doit contenir plus de 7 caractères'

            //         ]),
            //     ]
            //     ]);
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        // 'mapped' => false,
                        'attr' => [
                            'autocomplete' => 'new-password',
                            "placeholder" => "Entrez votre mot de passe",
                        ],
                        'label' => 'Mot de passe',
                        // 'label_attr' => ['class' => 'form-label  mt-4']
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Veuillez saisir un mot de passe',
                            ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Votre mot de passe doit contenir plus de 7 caractères',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                                // 'message' => 'Votre password doit contenir plus de 7 caractères'
    
                            ]),
                        ],
                    ],
                    'second_options' => [
                        'attr' => [
                            'class' => 'form-control',
                            "placeholder" => "Confirmez votre mot de passe",
                        ],
                        'label' => 'Confirmez Mot de passe',
                        'label_attr' => [
                            'class' => 'form-label  mt-4'
                        ]
                    ],
                    'invalid_message' => 'Les mots de passe ne correspondent pas.'
                ]);
            // ->add('submit', SubmitType::class, [
            //     'attr' => [
            //         'class' => 'btn btn-primary mt-4'
            //     ],
            //     'label' => 'Changer mon mot de passe'
            // ]);
     
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
       //
    }

}
