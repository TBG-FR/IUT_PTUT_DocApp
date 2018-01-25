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
                ])
                ->add('startTime', TimeType::class, [
                    'input' => 'datetime',
                    'mapped'=>false,
                ])

                ->add('endTimeHour', IntegerType::class, [
                    'required' => false,
                    'label' => ' ',
                    'mapped'=>false,
                    'attr' => ['readonly' => true],
                ])
                ->add('endTimeMinute', IntegerType::class, [
                    'required' => false,
                    'label' => ' ',
                    'mapped'=>false,
                    'attr' => ['readonly' => true],
                ])

                ->add('NbCrenaux', IntegerType::class, [
                    'required' => false,
                    'label' => 'Nombre Crenaux',
                    'mapped'=>false,
                    ])

                ->add('DureeCrenaux', DateIntervalType::class, array (
                'with_minutes'=>true,
                'with_hours'=>true,
                'with_days'=>false,
                'with_months'=>false,
                'with_years'=>false,
                'mapped'=>false,
                'hours' => range(1, 4),
            ))

            ->add('specialities', EntityType::class, [
                'class' => 'AppBundle\Entity\Speciality',
                'choice_label' => 'name',
                'label' => 'Spécialité(s)',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(SpecialityRepository $speciality) use($options) {
                    return $speciality->getFindByUserQueryBuilder($options['trait_choices']);
                }
            ])

            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Informations Additionnelles'])

            ->add('office', EntityType::class, [
                'class' => 'AppBundle\Entity\Office',
                'choice_label' => 'display',
                'label' => 'Cabinet',
                'multiple' => false,
                'query_builder' => function(OfficeRepository $repository) use($options) {
                    return $repository->getFindByUserQueryBuilder($options['trait_choices']);
                }
            ])

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