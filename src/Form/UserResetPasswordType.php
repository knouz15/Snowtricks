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
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'mapped' => false,
                        'attr' => [
                            'autocomplete' => 'new-password',
                            "placeholder" => "Entrez votre mot de passe",
                        ],
                        'label' => 'Mot de passe',
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Veuillez saisir un mot de passe',
                            ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Votre mot de passe doit contenir plus de 7 caractères',
                                'max' => 4096,
    
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
            
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
       //
    }

}



