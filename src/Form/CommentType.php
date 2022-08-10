<?php

namespace App\Form;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
            'label' => false,
            'attr' =>  [
                // 'class' => 'form-control',
                "placeholder" => "+ Laisser un commentaire ..."
            ],
            // 'label_attr' => [
            //     'class' => 'form-label mt-4'
            // ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Veuillez saisir un commentaire',
                ])
            ]
            ])  ;     
            // ->add('poster', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
