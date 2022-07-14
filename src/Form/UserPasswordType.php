<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('username', TextType::class, [
        //     'attr' => [
        //         'class' => 'form-control',
        //         'minlenght' => '2',
        //         'maxlenght' => '50',
        //     ],
        //     'label' => 'Username',
        //     'label_attr' => [
        //         'class' => 'form-label  mt-4'
        //     ],
        //     'constraints' => [
        //         new Assert\NotBlank(),
        //         new Assert\Length(['min' => 2, 'max' => 50])
        //     ]
        // ])
            ->add('username', TextType::class, [
                'attr' => [
                'class' => 'form-control',
                "placeholder" => "Entrez un username",
                'minlenght' => '2',
                'maxlenght' => '50',
                ],
                'label' => 'Nom d\'utilisateur',
                'label_attr' => [
                'class' => 'form-label  mt-4'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un username',
                    ]),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Votre username doit contenir plus de 3 caractères'])
                ]
            ])
            ->add('newPassword', PasswordType::class, [ 
                // 'attr' => ['class' => 'form-control'],
                'label' => 'Nouveau mot de passe',
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [
                    new Assert\NotBlank([
                    'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir plus de 7 caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        // 'message' => 'Votre password doit contenir plus de 7 caractères'

                    ]),
                ]
            ]);      
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
