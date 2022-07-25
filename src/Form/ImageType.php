<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
    //         ->add('name'
    //             // 'mapped' => false,
                
    // );
    ->add('name', FileType::class,[
        'mapped' => false,
        'label' => 'Uploadez une image',
        //     'required' => false,
        'constraints' => [
            new NotBlank([
                'message' => 'Veuillez joindre au moins une image',
            ]),
        ],
        'label' => 'Uploadez une image'
        //     'constraints' => [
                //         new File([
                //             'maxSize' => '1024k',
                //             'mimeTypes' => [
                //                 'application/jpg',
                //                 'application/png',
                //             ],
                //             'mimeTypesMessage' => 'Uploadez une image à format valide',
                //         ])
                //     ],
]);
    } 
 
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
