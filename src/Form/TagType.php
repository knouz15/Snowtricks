<?php

namespace App\Form;

use App\Entity\Tag;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
        ->add('name', TextType::class, [
            'label' => false,
            'required' => false,
            'constraints' => [
                new Regex([
                    'pattern' => '#^((?:https?:)?\/\/)?(?:www\.)?((?:youtube\.com|youtu\.be|dai\.ly|dailymotion\.com|vimeo\.com|player\.vimeo\.com))(\/(?:[\w\-]+\?v=|embed\/|video\/|embed\/video\/)?)([\w\-]+)(\S+)?$#',
                    // 'pattern' => '<iframe\s+.*?\s+src=("[^"]+").*?<\/iframe>',
                    //  'pattern' => '<iframe\s+.*?\s+src=(".*?").*?<\/iframe>',
            //         'message' => 'You can only use an iframe',
                ]),
            ],
        ]);
        // ->add('delete', ButtonType::class, [
        //     'label_html' => true,
        //     'label' => '<i class="fas fa-times"></i>',
        //     'attr' => [
        //         'data-action' => 'delete',
        //         'data-target' => '#trick_videos___name__',
        //     ],
        // ]
        
    }

            
            // , [
            //     'attr' => [
            //         'class' => 'form-control'
            //     ],
            //     'label' => 'iframe:',
                
            //     'label_attr' => [
            //         'class' => 'form-label mt-4'
            //     ],
            //     // 'constraints' => [
            //     //     new Assert\NotBlank()
            //     // ]
            // ]
            // [
             
                // 'label' => 'Vos vidéos',
            //     'attr' => [
            //         'placeholder' => 'Vidéo'
            //    ] ,
            //    'constraints' => [
            //     new NotBlank(),
            //     // new Url()
            //    ],
            //    'attr' => [
            //     'class' => 'form-control'
            // ],
            
            // 'label_attr' => [
            //     'class' => 'form-label mt-4'
            // ],]
    //         )
    //         ;

    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
