<?php

namespace UserBundle\Form;

use AppBundle\Form\ImageType;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->remove('username')
                ->add('email', EmailType::class, [
                    'label' => 'form.email',
                    'translation_domain' => 'FOSUserBundle'
                ])
                ->add('first_name', TextType::class, [
                    'label' => 'registration.first_name',
                    'translation_domain' => 'FOSUserBundle'
                ])
                ->add('last_name', TextType::class, [
                    'label' => 'registration.last_name',
                    'translation_domain' => 'FOSUserBundle'
                ])
                /*>add('avatar', ImageType::class, [
                    'required' => false
                ])*/
                ->setAction('/user');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName()
    {
        return 'app_user_registration';
    }

}
