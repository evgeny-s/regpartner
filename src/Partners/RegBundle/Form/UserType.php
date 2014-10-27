<?php

namespace Partners\RegBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Partners\RegBundle\Entity\User;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fio', 'text', array(
                'attr' => array('placeholder' => 'Firstname and Lastname'),
                'required' => false
            ))
            ->add('type', 'choice', array(
                'choices' => array(User::TYPE_USER => 'User', User::TYPE_PARTNER => 'Partner'),
                'label' => 'Registration type',
            ))
            ->add('phone', 'text', array(
                'attr' => array('placeholder' => '+375-29-123-45-67'),
                'required' => false
            ))
            ->add('login', 'text', array(
                'attr' => array('placeholder' => 'Login'),
            ))
            ->add('password', 'password', array(
                'attr' => array('placeholder' => 'Password'),
            ))
            ->add('email', 'text', array(
                'attr' => array('placeholder' => 'Email'),
                'required' => false
            ))
            ->add('partner_code', 'text', array(
                'attr' => array('placeholder' => 'Your partner`s code'),
                'required' => false
            ))
            ->add('userSites', 'collection', array(
                'type' => new SiteType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
        ;
    }

    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Partners\RegBundle\Entity\User',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'partners_regbundle_user';
    }
}
