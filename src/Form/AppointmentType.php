<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThan;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use DateTime;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateap', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'La date est obligatoire'
                ]),
               
                new GreaterThan([
                    'value' => 'today',
                    'message' => 'La date doit être supérieure à la date actuelle'
                ]),
            ]
        ])
        ->add('hour', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'heure est obligatoire'
                ]),
            ]
        ])
        ->add('ajouter', SubmitType::class)
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
