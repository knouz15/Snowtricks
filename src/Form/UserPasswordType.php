<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            ->add('username')
            ->add('newPassword', PasswordType::class, [ 
                'attr' => ['class' => 'form-control'],
                'label' => 'Nouveau mot de passe',
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [new Assert\NotBlank()]
            ]);
           
    }
}
