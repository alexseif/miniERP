<?php

namespace MeVisa\CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomersType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', 'text', array(
          'attr' => array(
            'placeholder' => 'Name'
          )
        ))
        ->add('email', 'email', array(
          'attr' => array(
            'placeholder' => 'Email'
          )
        ))
        ->add('phone', 'text', array(
          'attr' => array(
            'placeholder' => 'Phone'
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
      'data_class' => 'MeVisa\CRMBundle\Entity\Customers'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_crmbundle_customers';
  }

}
