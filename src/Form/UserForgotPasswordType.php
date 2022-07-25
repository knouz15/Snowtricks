<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
 
class UserForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ]); 
            
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
