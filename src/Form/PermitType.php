<?php

namespace App\Form;

use App\Entity\Permit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PermitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('newsletter',CheckboxType::class, ["required" => false])
        ->add('payment_online', CheckboxType::class, ["label" => "Paiement en ligne", "required" => false])
        ->add('team_schedule' , CheckboxType::class, ["label" => "Planning équipe", "required" => false])
        ->add('live_chat' , CheckboxType::class, ["label" => "Chat en ligne","required" => false])
        ->add('virtual_training' , CheckboxType::class, ["label" => "Entrainements virtuels", "required" => false])
        ->add('detailed_data' , CheckboxType::class, ["label" => "Donées Détaillées", "required" => false])   
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permit::class,
        ]);
    }
}
