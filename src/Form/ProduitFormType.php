<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\FormEvents ;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        
// Access the value of the "id_categorie" property


        $builder
            ->add('nom_prod')
            ->add('prix_prod')
            ->add('description_prod')
            ->add('quantite_prod')
            ->add('image', FileType::class, [
                'label' => 'choisir un fichier',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.jpg, .jpeg, .png',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M','mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG, JPEG or PNG image',
                    ])
                ],
            ])

            ->add('categorie',EntityType::class,
            ['class'=>Categorie::class,
            'choice_label'=>'nom_categorie'])
            ->add('save',SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

