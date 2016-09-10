<?php

namespace Admin\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        ->add('password')
        ->add('roles', 'choice', array(
          'choices' => $this->roles,
          'expanded' => true,
          'multiple' => true,
          'required' => false
        ))
        ->add('enabled', 'checkbox', array(
          'required' => false
        ))
        ->add('locale', 'locale', array(
          'choices' => array('English' => 'en', 'Russian' => 'ru'),
          'attr' => array(
            'class' => 'chosen'
          )
        ))
    ;
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $this->configureOptions($resolver);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
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
