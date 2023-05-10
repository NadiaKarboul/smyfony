<?php

namespace App\Form;

use App\Entity\Bonplan;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class BonplanFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_bonplan', null , [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Bonplan name',
                ],
                
            ])
            ->add('gouvernorat', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Gouvernoart',
                ],
            ])
            ->add('ville', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'City',
                ],
            ])
            ->add('rue', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Street',
                ],
            ])
            ->add('description', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'attr' => [
                    // 'class' => 'form-control',
                    'placeholder' => 'Type',
                ],
                'choices'  => [
                    'Garage' => 'Garage',
                    'Circuit' => 'Circuit',
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'imageFile',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.jpg, .jpeg, .png',
                    'class' => 'form-control form-choose',
                    
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG, JPEG or PNG image',
                    ])
                ],
            ])
        
            ->add('save',SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-solid',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bonplan::class,
        ]);
    }
}
