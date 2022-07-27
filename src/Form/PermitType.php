<?php

namespace App\Form;

use App\Entity\Permit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newsletter')
            ->add('payment_online')
            ->add('team_schedule')
            ->add('live_chat')
            ->add('virtual_training')
            ->add('detailed_data')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permit::class,
        ]);
    }
}
