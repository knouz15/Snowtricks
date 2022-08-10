<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    "placeholder" => "Entrez un username",
                    'minlenght' => '3',
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
                    new Length(['min' => 3, 'max' => 50, 'minMessage' => 'Votre username doit contenir au moins 3 caractères'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    "placeholder" => "Un mail de confirmation vous sera envoyé",
                    'minlenght' => '2',
                    'maxlenght' => '180',
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label  mt-4'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une email',
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une email valide',
                    ]),
                    new Length(['min' => 2, 'max' => 180])
                ]
            ])
            
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
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte la collecte de mes données personnelles dans le cadre de ce formulaire d\'inscription ',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
