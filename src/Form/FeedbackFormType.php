<?php

namespace App\Form;

use App\Entity\Feedback;
use Sbyaute\StarRatingBundle\Form\StarRatingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rate',StarRatingType::class,[
                'label' => 'Rating',
                'stars' => 5,
            ])
            ->add('commentaire',null,[
                'attr' => [
                    'placeholder' => 'Write your Feedback ',
                ],
            ])
            ->add('report', CheckboxType::class,[
                'label' => 'Report',
            ])
            ->add('save',SubmitType::class,[
                'label' => 'Post Comment',
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
