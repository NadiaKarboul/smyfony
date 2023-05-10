<?php

namespace App\Form;

use App\Entity\Event;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event')
            ->add('date_debut',DateType::class, array(
                'widget' => 'single_text',))
                ->add('date_fin',DateType::class, array(
                    'widget' => 'single_text',))
            ->add('lieu_event')
            ->add('type_event')
            ->add('image_event', FileType::class, [
                'label' => 'choisir un fichier',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.jpg, .jpeg, .png',
                ],    'constraints' => [
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
            ->add('prix_event')
            ->add('save',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
