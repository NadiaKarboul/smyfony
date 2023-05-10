<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_categorie')
            ->add('description_categorie')
            ->add('type_categorie', ChoiceType::class, [
                'choices' => [
                    'Mode' => 'mode',
                    'Pièces de rechange' => 'pieces_de_rechange',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            
            ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
