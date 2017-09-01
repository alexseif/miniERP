<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsersPasswordType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('plainPassword', RepeatedType::class, array(
          'type' => PasswordType::class,
          'options' => array('translation_domain' => 'FOSUserBundle'),
          'first_options' => array('label' => 'form.password'),
          'second_options' => array('label' => 'form.password_confirmation'),
          'invalid_message' => 'fos_user.password.mismatch',
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
      'data_class' => 'AppBundle\Entity\User',
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'admin_users_password';
  }

}
