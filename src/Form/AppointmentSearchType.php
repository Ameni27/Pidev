<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;




class AppointmentSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('status', ChoiceType::class, [
            'required' => false,
            'choices' => [
                'Tous' => null,
                'Confirmé' => true,
                'En attente' => false,
            ],
            'placeholder' => 'Choisir le statut',
        ])
        ->add('patientName', null, [
            'required' => false,
        ])
        ->add('appointmentDate', null, [
            'required' => false,
        ])

            
            ->add('appointmentTime')
            ->add('search', SubmitType::class);

            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
        ]);
    }
}
