<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
 
class UserForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('username', TextType::class, [
                'attr' => [
                'class' => 'form-control',
                "placeholder" => "Entrez votre username",
                'label' => 'Nom d\'utilisateur',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre username',
                    ]),
                ]
            ]); 
            
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        //
    }
}


