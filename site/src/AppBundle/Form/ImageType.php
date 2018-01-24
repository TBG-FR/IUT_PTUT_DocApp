<?php

namespace AppBundle\Form;

use AppBundle\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'label' => 'Image',
            'label_attr' => [
                'style' => 'display:none;'
            ],
            'attr' => [
                'class' => 'form-control'
            ]
        ]);
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
            'data_class' => Image::class
        ));
    }
}