<?php

namespace AppBundle\Form;

use AppBundle\Entity\Appointment;
use AppBundle\Entity\RegularAppointment;
use AppBundle\Repository\OfficeRepository;
use AppBundle\Repository\SpecialityRepository;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, [
                    'input' => 'datetime'
                ])

                ->add('name', TextType::class, ['required' => true, 'label' => 'Nom, Prénom ou Pseudonyme'])

                ->add('mail', EmailType::class, ['required' => true, 'label' => 'Adresse Email'])

                ->add('content', TextareaType::class, ['required' => true, 'label' => 'Feedback (Améliorations, Critiques, Bugs, ...)'])

                ->add('submit', SubmitType::class, ['label' => 'Valider']);

    }

}