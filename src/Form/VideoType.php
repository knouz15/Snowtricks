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
                // 'placeholder' => 'lolo',
                'constraints' => [
                    new Regex([
                        // 'pattern' => '#(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))#',
                        
                        'pattern' => '#(https?:\/\/)?(www.)?(youtube\.com|youtu\.be|youtube-nocookie\.com)\/(?:embed\/|v\/|watch\?v=|watch\?list=(.*)&v=)?((\w|-){11})(&list=(\w+)&?)?#',
                        'message' => 'Non valide. ',
                    ]),
                ], 
                'label' => 'Votre url embed'
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
