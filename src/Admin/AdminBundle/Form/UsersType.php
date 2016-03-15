<?php

namespace Admin\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsersType extends AbstractType
{

    private $roles;

    function __construct($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', 'text', array(
                    'attr' => array(
                        'placeholder' => 'Name'
                    )
                ))
                ->add('email', 'email', array(
                    'attr' => array(
                        'placeholder' => 'Email'
                    )
                ))
                ->add('roles', 'choice', array(
                    'choices' => $this->roles,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                ))
                ->add('enabled', 'checkbox', array(
                    'required' => false
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Admin\AdminBundle\Entity\User',
            'roles' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_users';
    }

}
