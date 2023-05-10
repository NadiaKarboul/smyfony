<?php

namespace App\Form;

use App\Entity\Event;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class QuantiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
        ->add('quantity', IntegerType::class, [
            'label' => 'Quantité',
            'attr' => [
                'min' => 1, // valeur minimale du sélecteur
                'max' => 10, // valeur maximale du sélecteur
                'step' => 1, // étape du sélecteur
                'class' => 'form-control' // classe CSS pour le sélecteur
            ]
        ])
            ->add('reserver',SubmitType::class)
            
        ;
    }


}