<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('plainPassword');
        $builder->setAction('/user/edit');
    }

    public function getParent()
    {
        return UserType::class;
    }
}