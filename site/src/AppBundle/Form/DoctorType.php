<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone', TextType::class)
                ->add('address', AddressType::class)
                ->add('specialities', EntityType::class, [
                    'class' => 'AppBundle\Entity\Speciality',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Spécialités'
                ])
                ->add('save', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-primary'],
                    'label' => 'S\'inscrire'
                ])
                ->setAction('/doctors/register')
                ->remove('username');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Doctor'
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }

    public function getParent()
    {
        return UserType::class;
    }
}