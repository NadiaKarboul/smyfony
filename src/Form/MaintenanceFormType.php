<?php

namespace App\Form;

use App\Entity\Maintenance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MaintenanceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // ->add('date_maintenance')
            ->add('panne_moteur', CheckboxType::class, [
                'label' => 'panne_moteur',
                'value' => 'yes',
                'required' => false,
            ])
            ->add('pompe_a_eau', CheckboxType::class, [
                'label' => 'pompe_a_eau',
                'required' => false,
            ])
            ->add('patin', CheckboxType::class, [
                'label' => 'patin',
                'required' => false,
            ])
            ->add('essuie_glace', CheckboxType::class, [
                'label' => 'essuie_glace',
                'required' => false,
            ])
            ->add('radiateur', CheckboxType::class, [
                'label' => 'radiateur',
                'required' => false,
            ])
            ->add('ventilateur', CheckboxType::class, [
                'label' => 'ventilateur',
                'required' => false,
            ])
            ->add('duride', CheckboxType::class, [
                'label' => 'duride',
                'required' => false,
            ])
            ->add('fuite_d_huile', CheckboxType::class, [
                'label' => 'fuite_d_huile',
                'required' => false,
            ])
            ->add('vidange', CheckboxType::class, [
                'label' => 'vidange',
                'required' => false,
            ])
            ->add('filtre', CheckboxType::class, [
                'label' => 'filtre',
                'required' => false,
            ])
            ->add('batterie', CheckboxType::class, [
                'label' => 'batterie',
                'required' => false,
            ])
            ->add('amortisseur', CheckboxType::class, [
                'label' => 'amortisseur',
                'required' => false,
            ])
            ->add('frein_main', CheckboxType::class, [
                'label' => 'frein_main',
                'required' => false,
            ])
            ->add('feu_d_eclairage', CheckboxType::class, [
                'label' => 'feu_d_eclairage',
                'required' => false,
            ])
          
           // ->add('user')
           ->add('ajouter',SubmitType::class)
        ;
       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Maintenance::class,
        ]);
    }
}
