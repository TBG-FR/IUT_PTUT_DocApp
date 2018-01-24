<?php

namespace AppBundle\Form;

use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Repository\OfficeRepository;
use AppBundle\Repository\SpecialityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentMultipleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, [
                    'input' => 'datetime',
                    'mapped'=>false,
                ])
                ->add('startTime', TimeType::class, [
                    'input' => 'datetime',
                    'mapped'=>false,
                ])
                ->add('endTime', TimeType::class, [
                    'input' => 'datetime',
                    'mapped'=>false,
                ])

                ->add('NbCrenaux', IntegerType::class, ['required' => false, 'label' => 'NbCrenaux',
                    'mapped'=>false,])
                ->add('DureeCrenaux', DateIntervalType::class, array (
                'with_minutes'=>true,
                'with_hours'=>true,
                'with_days'=>false,
                'with_months'=>false,
                'with_years'=>false,
                'mapped'=>false,
                'hours' => range(1, 4),
            ))

                ->add('submit', SubmitType::class, ['label' => 'Ajouter ces RDV']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Appointment::class,
            'trait_choices' => null
        ));
    }
}