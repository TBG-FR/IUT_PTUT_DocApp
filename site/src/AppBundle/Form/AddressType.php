<?php

namespace AppBundle\Form;

use AppBundle\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Nom']);
        $builder->add('line1', TextType::class, ['label' => 'Ligne 1']);
        $builder->add('line2', TextType::class, ['required' => false, 'label' => 'Ligne 2']);
        $builder->add('line3', TextType::class, ['required' => false, 'label' => 'Ligne 3']);
        $builder->add('state', TextType::class, ['required' => false, 'label' => 'Etat']);
        $builder->add('zip', TextType::class, ['label' => 'Code postal']);
        $builder->add('city', TextType::class, ['label' => 'Ville']);
        $builder->add('country', TextType::class, ['required' => false, 'label' => 'Pays']);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class
        ));
    }
}