<?php

namespace App\Form;

use App\Entity\Lignepanier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class LignePanierFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('quantite', IntegerType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'La quantité est obligatoire !']),
                new Assert\Positive(['message' => 'La quantité doit être un nombre positif !']),
                new Assert\Range([
                    'min' => 1,
                    'minMessage' => 'Au minimum la quantité doit être égale à 1',
                ]),
            ],
        ])
 
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lignepanier::class,
        ]);
    }
}
