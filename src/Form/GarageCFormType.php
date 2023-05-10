<?php

namespace App\Form;

use App\Entity\Garagec;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class GarageCFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_garage')
           ->add('adresse')
            ->add('numero')
            ->add('panne_moteur')
            ->add('pompe_a_eau')
            ->add('patin')
            ->add('essuie_glace')
            ->add('radiateur')
            ->add('ventilateur')
            ->add('duride')
            ->add('fuite_d_huile')
            ->add('vidange')
            ->add('filtre')
            ->add('batterie')
            ->add('amortisseur')
            ->add('frein_main')
            ->add('feu_d_eclairage')
            ->add('taux_de_reduction')
            ->add('image', FileType::class, [
                'label' => 'choisir un fichier',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.jpg, .jpeg, .png',
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
            
            ->add('ajouter',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Garagec::class,
        ]);
    }
}
