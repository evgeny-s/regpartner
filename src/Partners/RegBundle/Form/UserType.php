<?php

namespace Partners\RegBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Partners\RegBundle\Entity\User;

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
                'label_attr' =>  array('class' => 'col-md-4 control-label')
            ))
            ->add('type', 'choice', array(
                'choices' => array(User::TYPE_USER => 'User', User::TYPE_PARTNER => 'Partner'),
                'label_attr' =>  array('class' => 'col-md-4 control-label'),
                'label' => 'Registration type'
            ))
            ->add('phone', 'text', array(
                'attr' => array('placeholder' => '+375-29-123-45-67'),
                'label_attr' =>  array('class' => 'col-md-4 control-label')
            ))
            ->add('login', 'text', array(
                'attr' => array('placeholder' => 'Login'),
                'label_attr' =>  array('class' => 'col-md-4 control-label')
            ))
            ->add('password', 'password', array(
                'attr' => array('placeholder' => 'Password'),
                'label_attr' =>  array('class' => 'col-md-4 control-label')
            ))
            ->add('email', 'text', array(
                'attr' => array('placeholder' => 'Email'),
                'label_attr' =>  array('class' => 'col-md-4 control-label')
            ))
            ->add('userSites', 'collection', array(
                'type' => new SiteType(),
                'allow_add' => true,
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
            'data_class' => 'Partners\RegBundle\Entity\User'
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
