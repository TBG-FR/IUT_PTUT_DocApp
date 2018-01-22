<?php

namespace AppBundle\Form;

use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Repository\OfficeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('startTime', TimeType::class, [
                    'input' => 'datetime'
                ])
                ->add('endTime', TimeType::class)
                ->add('regular_appointment', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Créneau récurrent',
                    'mapped' => false
                ])
                ->add('frequency', IntegerType::class, [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Fréquence de répétition',
                    'attr' => [
                        'style' => 'display:none;'
                    ],
                    'label_attr' => [
                        'style' => 'display:none;'
                    ]
                ])
                ->add('frequency_type', ChoiceType::class, [
                    'choices' => RegularAppointment::getFancyFrenquencyTypeList(),
                    'mapped' => false,
                    'label' => 'Répétition',
                    'required' => false,
                    'attr' => [
                        'style' => 'display:none;'
                    ],
                    'label_attr' => [
                        'style' => 'display:none;'
                    ]
                ])
                ->add('description', TextareaType::class, ['required' => false, 'label' => 'Commentaire'])
                ->add('office', EntityType::class, [
                    'class' => 'AppBundle\Entity\Office',
                    'choice_label' => 'display',
                    'label' => 'Cabinet',
                    'multiple' => false,
                    'query_builder' => function(OfficeRepository $repository) use($options) {
                        return $repository->getFindByUserQueryBuilder($options['trait_choices']);
                    }
                ])
                ->add('submit', SubmitType::class, ['label' => 'Ajouter']);
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