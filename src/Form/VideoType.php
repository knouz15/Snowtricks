<?php

namespace App\Form;

use App\Entity\Video;
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

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('url',TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '#(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))#',
                        'message' => 'Ceci n\'est pas une iframe',
                    ]),
                ],
                'label' => 'Entrez une iframe'
            ]
         
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
